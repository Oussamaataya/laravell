# AI/train_model.py
import pickle
from sklearn.naive_bayes import MultinomialNB
from sklearn.metrics import classification_report, accuracy_score
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import CountVectorizer

# Charger l'échantillon préparé
df = pd.read_csv(r'C:\Users\yosry\Downloads\laravell\AI\dataset_sample.csv')  # <-- crée ce CSV après prepare_dataset.py
# Supprimer les lignes avec texte manquant
df = df.dropna(subset=['clean_text'])

# Split train/test
X_train, X_test, y_train, y_test = train_test_split(
    df['clean_text'], df['sentiment'], test_size=0.2, random_state=42
)

# Vectorisation
vectorizer = CountVectorizer()
X_train_vec = vectorizer.fit_transform(X_train)
X_test_vec = vectorizer.transform(X_test)

# Entraîner le modèle
model = MultinomialNB()
model.fit(X_train_vec, y_train)

# Évaluer le modèle
y_pred = model.predict(X_test_vec)
print("Accuracy:", accuracy_score(y_test, y_pred))
print(classification_report(y_test, y_pred))

# Sauvegarder modèle et vectorizer
pickle.dump(model, open('AI/sentiment_model.pkl', 'wb'))
pickle.dump(vectorizer, open('AI/vectorizer.pkl', 'wb'))

print("✅ Modèle et vectorizer sauvegardés")
