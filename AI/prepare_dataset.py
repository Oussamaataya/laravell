
# Charger dataset
import pandas as pd
import re
import pickle
from sklearn.model_selection import train_test_split
from sklearn.feature_extraction.text import CountVectorizer

# Charger dataset
df = pd.read_csv(r'C:\Users\yosry\Downloads\laravell\AI\french_tweets.csv')  # remplacez par le chemin réel

# Supprimer lignes manquantes
df = df.dropna(subset=['text', 'label'])

# Créer colonne sentiment
df['sentiment'] = df['label'].apply(lambda x: 'negatif' if x == 0 else 'positif')

# Nettoyage sécurisé
def clean_text(text):
    text = str(text)
    text = text.lower()
    text = re.sub(r'http\S+', '', text)
    text = re.sub(r'[^a-zA-Z\s]', '', text)
    text = re.sub(r'\s+', ' ', text).strip()
    return text

# Échantillon pour accélérer
df_sample = df.sample(n=100000, random_state=42)
df_sample['clean_text'] = df_sample['text'].apply(clean_text)

# Split train/test
X_train, X_test, y_train, y_test = train_test_split(
    df_sample['clean_text'], df_sample['sentiment'], test_size=0.2, random_state=42
)

# Vectorisation
vectorizer = CountVectorizer()
X_train_vec = vectorizer.fit_transform(X_train)

# Sauvegarder vectorizer
pickle.dump(vectorizer, open('AI/vectorizer.pkl', 'wb'))

print("✅ Dataset préparé et vectorizer sauvegardé (100k lignes)")
# Après le nettoyage et la sélection d'échantillon
df_sample = df.sample(n=100000, random_state=42)
df_sample['clean_text'] = df_sample['text'].apply(clean_text)

# Sauvegarder l'échantillon
df_sample.to_csv('AI/dataset_sample.csv', index=False)
print("✅ Échantillon sauvegardé dans AI/dataset_sample.csv")
