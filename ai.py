from flask import Flask, jsonify, request
import pandas as pd
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.preprocessing import LabelEncoder

app = Flask(__name__)

# 1. Expanded Dummy Data
food_data = [
    {"name": "Nasi Lemak", "food_type": "Rice", "meal_type": "Breakfast", "cuisine": "Malay", "taste": "Savoury", "price": 5.00},
    {"name": "Fried Rice", "food_type": "Rice", "meal_type": "Lunch", "cuisine": "Chinese", "taste": "Spicy", "price": 6.50},
    {"name": "Chicken Chop", "food_type": "Western", "meal_type": "Dinner", "cuisine": "Western", "taste": "Savoury", "price": 15.00},
    {"name": "Sushi", "food_type": "Rice", "meal_type": "Lunch", "cuisine": "Japanese", "taste": "Sweet", "price": 10.00},
    {"name": "Spaghetti", "food_type": "Western", "meal_type": "Dinner", "cuisine": "Italian", "taste": "Savoury", "price": 12.00},
    {"name": "Burger", "food_type": "Fast Food", "meal_type": "Lunch", "cuisine": "American", "taste": "Savoury", "price": 8.00},
    {"name": "Maggi Goreng", "food_type": "Noodles", "meal_type": "Dinner", "cuisine": "Malay", "taste": "Spicy", "price": 4.50},
    {"name": "Dim Sum", "food_type": "Rice", "meal_type": "Breakfast", "cuisine": "Chinese", "taste": "Savoury", "price": 6.00}
]

df = pd.DataFrame(food_data)

# 2. Pre-process the data for the AI
# We convert categories into numbers so the math works
le_map = {}
features = ['food_type', 'meal_type', 'cuisine', 'taste']

df_encoded = df.copy()
for col in features:
    le = LabelEncoder()
    df_encoded[col] = le.fit_transform(df[col])
    le_map[col] = le # Save for user input transformation

@app.route("/recommend", methods=["POST"])
def recommend():
    try:
        # Get budget and handle empty strings safely
        raw_budget = request.form.get('budget')
        if not raw_budget or raw_budget == "":
            budget_limit = 999.0
        else:
            budget_limit = float(raw_budget)

        # Get User Input from PHP
        user_pref = {
            'food_type': request.form.get('food_type'),
            'meal_type': request.form.get('meal_type'),
            'cuisine': request.form.get('cuisine'),
            'taste': request.form.get('taste'),
            'budget': budget_limit
        }

        # Transform User Input into a Vector
        user_vector = []
        for col in features:
            val = user_pref[col]
            # Handle cases where user leaves a field blank or picks unknown
            if val in le_map[col].classes_:
                user_vector.append(le_map[col].transform([val])[0])
            else:
                user_vector.append(-1) # Unknown/Neutral value

        # 3. THE AI STEP: Calculate Similarity
        # Compare user vector against all foods in the database
        food_vectors = df_encoded[features].values
        similarities = cosine_similarity([user_vector], food_vectors)[0]

        # Add similarity scores to our dataframe
        df['similarity'] = similarities
        
        # Convert to percentage (0.85 -> 85%)
        df['match_percentage'] = (df['similarity'] * 100).round(1)
        
        # Filter by Budget and Sort
        recommendations = df[df['price'] <= user_pref['budget']].sort_values(by='similarity', ascending=False)

        # Return results including the match percentage
        return jsonify(recommendations.head(3).to_dict(orient='records'))
        
        # 4. Filter by Budget first, then sort by AI Similarity
        recommendations = df[df['price'] <= user_pref['budget']].sort_values(by='similarity', ascending=False)

        # Filter by Budget (using the high default if none was provided)
        recommendations = df[df['price'] <= user_pref['budget']].sort_values(by='similarity', ascending=False)

        return jsonify(recommendations.head(3).to_dict(orient='records'))

    except Exception as e:
        return jsonify({"error": str(e)})# Return top 3 AI-ranked results
        return jsonify(recommendations.head(3).to_dict(orient='records'))

if __name__ == "__main__":
    app.run(debug=True, port=5000)