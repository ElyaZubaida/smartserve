from flask import Flask, jsonify, request
import pandas as pd
import mysql.connector
from flask_cors import CORS
from scipy.sparse import csr_matrix
from sklearn.neighbors import NearestNeighbors

app = Flask(__name__)
CORS(app)

def get_db_connection():
    return mysql.connector.connect(
        host="localhost",
        port=3307, 
        user="root",
        password="",
        database="smartservedb"
    )

@app.route("/recommend", methods=["POST"])
def recommend():
    try:
        db = get_db_connection()
        # Ensure we get the menuID for linking and menuImage for display
        query = """
            SELECT `menuID`, `menuName` as name, `menuImage`, `menuCategory`, `foodType`, 
                `mealType`, `cuisine`, `flavour`, `portion`, `menuPrice` as price, `menuDescription`
            FROM menus
            WHERE `is_deleted` = 0 AND `menuAvailability` = 1
        """
        df = pd.read_sql(query, db)
        db.close()

        # Capture Inputs from PHP
        u_cat     = request.form.get('menu_category', '').lower()
        u_type    = request.form.get('food_type', '').lower()
        u_meal    = request.form.get('meal_type', '').lower()
        u_cuisine = request.form.get('cuisine', '').lower()
        u_flav    = request.form.get('flavour', '').lower()
        u_portion = request.form.get('portion', '').lower()
        
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
            if str(row['portion']).lower() == u_portion: score += 1
            
            match_pct = round((score / 8) * 100, 1)
            # Budget Penalty
            if float(row['price']) > u_budget:
                match_pct = round(match_pct * 0.3, 1)
            return match_pct

        df['match_percentage'] = df.apply(calculate_score, axis=1)
        max_match = df['match_percentage'].max()

        # Threshold Logic: If best match < 70%, show Popular items under budget
        if max_match < 70:
            recommendations = df[df['price'] <= u_budget].head(3).copy()
            recommendations['match_percentage'] = "Popular"
        else:
            recommendations = df.sort_values(by='match_percentage', ascending=False).head(3)

        # Convert numeric types for JSON
        recommendations['menuID'] = recommendations['menuID'].astype(int)
        recommendations['price'] = recommendations['price'].astype(float)
        
        return jsonify(recommendations.to_dict(orient='records'))

    except Exception as e:
        return jsonify({"error": str(e)}), 500


# Route History (KNN) biarkan sama
@app.route("/recommend_history", methods=["GET"])
def recommend_history():
    try:
        student_id = request.args.get('student_id')
        db = get_db_connection()
        query = "SELECT o.student_ID, om.menuID FROM orders o JOIN order_menu om ON o.order_ID = om.order_ID WHERE o.order_status = 'Completed'"
        df = pd.read_sql(query, db)
        user_history = df[df['student_ID'] == int(student_id)]
        if user_history.empty:
            db.close()
            return jsonify([]) 
        df['rating'] = 1 
        user_item_matrix = df.pivot_table(index='menuID', columns='student_ID', values='rating').fillna(0)
        X = csr_matrix(user_item_matrix.values)
        model = NearestNeighbors(metric='cosine', algorithm='brute')
        model.fit(X)
        last_item_id = int(user_history.tail(1)['menuID'].values[0])
        try:
            menu_idx = user_item_matrix.index.get_loc(last_item_id)
            k_neighbors = min(4, len(user_item_matrix))
            distances, indices = model.kneighbors(X[menu_idx], n_neighbors=k_neighbors)
            recommend_ids = [int(user_item_matrix.index[i]) for i in indices.flatten()[1:]]
        except:
            recommend_ids = []
        db.close()
        return jsonify(recommend_ids)
    except:
        return jsonify([])

if __name__ == "__main__":
    app.run(debug=True, port=5000)