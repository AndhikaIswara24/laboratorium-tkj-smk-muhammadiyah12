import os
import sys
import numpy as np
import pandas as pd
import mysql.connector
from sklearn.naive_bayes import GaussianNB
from sklearn.model_selection import cross_val_score, StratifiedKFold
from sklearn.preprocessing import LabelEncoder
import warnings
warnings.filterwarnings('ignore')

# Add flask_api to sys.path to import DB configuration & feature names
sys.path.append(os.path.join(os.path.dirname(__file__), 'flask_api'))
from config import DB_CONFIG
from flask_api.app import FEATURE_COLUMNS, CATEGORICAL_COLUMNS, TARGET_COLUMN

# Connect to DB and fetch dataset
conn = mysql.connector.connect(**DB_CONFIG)
cursor = conn.cursor(dictionary=True)
columns = FEATURE_COLUMNS + [TARGET_COLUMN]
query = f"SELECT {', '.join(columns)} FROM t_naive_bayes_dataset WHERE {TARGET_COLUMN} IS NOT NULL"
cursor.execute(query)
rows = cursor.fetchall()
cursor.close()
conn.close()

if not rows:
    print("Dataset empty!")
    sys.exit(1)

df = pd.DataFrame(rows, columns=columns)

# Encode categorical features and target label
for col in CATEGORICAL_COLUMNS:
    le = LabelEncoder()
    df[col] = le.fit_transform(df[col].astype(str))

le_target = LabelEncoder()
df[TARGET_COLUMN] = le_target.fit_transform(df[TARGET_COLUMN].astype(str))

X = df[FEATURE_COLUMNS].values.astype(float)
y = df[TARGET_COLUMN].values

# Instantiate Gaussian Naive Bayes Model
model = GaussianNB()

# 5-Fold Stratified K-Fold Cross Validation
skf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
scores = cross_val_score(model, X, y, cv=skf, scoring='accuracy')

print("=== HASIL KINERJA 5-FOLD CROSS VALIDATION ===")
for i, score in enumerate(scores, 1):
    print(f"Fold {i}: {score * 100:.2f}% ({score:.4f})")

print("---------------------------------------------")
print(f"Array Scores: {scores}")
print(f"Rata-rata (Mean CV): {scores.mean() * 100:.2f}% ({scores.mean():.4f})")
print(f"Standar Deviasi : ± {scores.std() * 100:.2f}% ({scores.std():.4f})")
