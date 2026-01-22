from flask import Flask, jsonify, request
import pandas as pd
import mysql.connector
from flask_cors import CORS
from scipy.sparse import csr_matrix
from sklearn.neighbors import NearestNeighbors

app = Flask(__name__)
CORS(app)

# Database configuration: Connects Python to your XAMPP MySQL database
def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        port=3307, 
        user="root",
        password="",
        database="smartservedb"
    )

# --- ROUTE 1: CONTENT-BASED FILTERING (Based on User Cravings/Input) ---
@app.route("/recommend", methods=["POST"])
def recommend():
    try:
        db = get_db_connection()
        query = """
            SELECT `menuID`, `menuName` as name, `menuImage`, `menuCategory`, `foodType`, 
                `mealType`, `cuisine`, `flavour`, `portion`, `menuPrice` as price, `menuDescription`
            FROM menus
            WHERE `is_deleted` = 0 AND `menuAvailability` = 1
        """
        df = pd.read_sql(query, db)
        db.close()

        # Capture Inputs
        u_cat     = request.form.get('menu_category', '').lower()
        u_type    = request.form.get('food_type', '').lower()
        u_meal    = request.form.get('meal_type', '').lower()
        u_cuisine = request.form.get('cuisine', '').lower()
        u_flav    = request.form.get('flavour', '').lower()
        u_hunger  = request.form.get('hunger_level', '').lower() # Map to portion
        
        raw_budget = request.form.get('budget')
        u_budget = float(raw_budget) if raw_budget and raw_budget.strip() != "" else 999.0

        # AI Scoring Logic
        def calculate_score(row):
            score = 0
            if str(row['menuCategory']).lower() == u_cat: score += 3
            if str(row['foodType']).lower() == u_type: score += 1
            if str(row['mealType']).lower() == u_meal: score += 1
            if str(row['cuisine']).lower() == u_cuisine: score += 1
            if str(row['flavour']).lower() == u_flav: score += 1
            if str(row['portion']).lower() == u_hunger: score += 1
            
            match_pct = round((score / 8) * 100, 1)
            if float(row['price']) > u_budget:
                match_pct = round(match_pct * 0.3, 1)
            return match_pct

        df['match_percentage'] = df.apply(calculate_score, axis=1)
        max_match = df['match_percentage'].max()

        # --- THRESHOLD LOGIC (< 70% match) ---
        if max_match < 70:
            # Step 1: Filter by budget first
            available_df = df[df['price'] <= u_budget].copy()

            if available_df.empty:
                recommendations = df.sort_values(by='price').head(3).copy()
                recommendations['match_percentage'] = "budget-friendly options"
            else:
                # Step 2: Try to find a match that combines CATEGORY + one other keyword
                # We prioritize Category because it's the main thing a student wants to eat
                secondary_checks = [
                    ('portion', u_hunger, f"{u_hunger}"),
                    ('foodType', u_type, f"{u_type}"),
                    ('flavour', u_flav, f"{u_flav}")
                ]

                best_match_found = False
                
                # Check for Category + (Portion OR Type OR Flavour)
                for col, val, label in secondary_checks:
                    if val and val.strip() != "":
                        # Try to match BOTH Category and the secondary attribute
                        combo_match = available_df[
                            (available_df['menuCategory'].str.lower() == u_cat) & 
                            (available_df[col].str.lower() == val)
                        ].copy()

                        if not combo_match.empty:
                            recommendations = combo_match.head(3)
                            # Custom message showing the combination
                            recommendations['match_percentage'] = f"great {label} {u_cat} options!"
                            best_match_found = True
                            break
                
                # Step 3: If no 2-keyword combo exists, fall back to just the Category
                if not best_match_found:
                    cat_only = available_df[available_df['menuCategory'].str.lower() == u_cat].copy()
                    if not cat_only.empty:
                        recommendations = cat_only.head(3)
                        recommendations['match_percentage'] = f"popular {u_cat} dishes"
                    else:
                        recommendations = available_df.head(3)
                        recommendations['match_percentage'] = "top picks within your budget"
        else:
            # Normal High Match
            recommendations = df.sort_values(by='match_percentage', ascending=False).head(3)

        recommendations['menuID'] = recommendations['menuID'].astype(int)
        recommendations['price'] = recommendations['price'].astype(float)
        
        return jsonify(recommendations.to_dict(orient='records'))

    except Exception as e:
        return jsonify({"error": str(e)}), 500


# --- ROUTE 2: COLLABORATIVE FILTERING (KNN Based on Purchase History) ---
@app.route("/recommend_history", methods=["GET"])
def recommend_history():
    try:
        student_id = request.args.get('student_id')
        db = get_db_connection()
        
        # Get history of all completed orders to find patterns between different users
        query = "SELECT o.student_ID, om.menuID FROM orders o JOIN order_menu om ON o.order_ID = om.order_ID WHERE o.order_status = 'Completed'"
        df = pd.read_sql(query, db)
        
        user_history = df[df['student_ID'] == int(student_id)]
        
        # If student is new (no history), return empty list (PHP will then show global trending items)
        if user_history.empty:
            db.close()
            return jsonify([]) 

        # Create a User-Item Matrix: Rows = Menus, Columns = Students
        # This allows the AI to see which menus are "similar" based on who bought them
        df['rating'] = 1 
        user_item_matrix = df.pivot_table(index='menuID', columns='student_ID', values='rating').fillna(0)
        
        # Convert to Sparse Matrix for faster mathematical calculation
        X = csr_matrix(user_item_matrix.values)
        
        # --- KNN MODEL (Unsupervised Machine Learning) ---
        # We use Cosine Similarity to find items that are "mathematically close" in taste patterns
        model = NearestNeighbors(metric='cosine', algorithm='brute')
        model.fit(X)
        
        # Find the last item the user bought
        last_item_id = int(user_history.tail(1)['menuID'].values[0])
        
        try:
            # Find the "Nearest Neighbors" (3 most similar items) to that last purchase
            menu_idx = user_item_matrix.index.get_loc(last_item_id)
            k_neighbors = min(4, len(user_item_matrix))
            distances, indices = model.kneighbors(X[menu_idx], n_neighbors=k_neighbors)
            
            # Extract the IDs of the recommended items (skipping the first index which is the item itself)
            recommend_ids = [int(user_item_matrix.index[i]) for i in indices.flatten()[1:]]
        except:
            recommend_ids = []
            
        db.close()
        return jsonify(recommend_ids)
    except:
        return jsonify([])

if __name__ == "__main__":
    # Runs the AI server on port 5000
    app.run(debug=True, port=5000)