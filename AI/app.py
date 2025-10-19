from flask import Flask, request, jsonify
import pickle
import re

app = Flask(__name__)

# Charger le modèle et le vectorizer entraînés
model = pickle.load(open('AI/sentiment_model.pkl', 'rb'))
vectorizer = pickle.load(open('AI/vectorizer.pkl', 'rb'))

def clean_text(text):
    text = text.lower()
    text = re.sub(r'http\S+', '', text)       # enlever URLs
    text = re.sub(r'[^a-zA-Z\s]', '', text)  # enlever ponctuation
    text = re.sub(r'\s+', ' ', text).strip() # enlever espaces multiples
    return text

@app.route('/sentiment', methods=['POST'])
def sentiment():
    data = request.json
    text = data.get('text', '')

    if not text:
        return jsonify({'error': 'No text provided'}), 400

    # Nettoyer et vectoriser
    clean = clean_text(text)
    vectorized = vectorizer.transform([clean])

    # Prédire le sentiment
    pred = model.predict(vectorized)[0]

    return jsonify({'sentiment': pred})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
