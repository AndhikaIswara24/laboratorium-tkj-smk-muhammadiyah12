import os
import json
import numpy as np
import pandas as pd
import mysql.connector
from sklearn.naive_bayes import GaussianNB
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.metrics import accuracy_score

from config import DB_CONFIG
from app import FEATURE_COLUMNS, CATEGORICAL_COLUMNS, TARGET_COLUMN, CLASS_LABELS

# Connect to DB
conn = mysql.connector.connect(**DB_CONFIG)
cursor = conn.cursor(dictionary=True)
columns = FEATURE_COLUMNS + [TARGET_COLUMN]
query = f"SELECT {', '.join(columns)} FROM t_naive_bayes_dataset WHERE {TARGET_COLUMN} IS NOT NULL"
cursor.execute(query)
rows = cursor.fetchall()
cursor.close()
conn.close()

if not rows:
    print(json.dumps({'error': 'empty dataset'}))
    exit(1)

df = pd.DataFrame(rows, columns=columns)

# Encode categoricals
encoders = {}
for col in CATEGORICAL_COLUMNS:
    le = LabelEncoder()
    df[col] = le.fit_transform(df[col].astype(str))
    encoders[col] = le

le_target = LabelEncoder()
le_target.fit(CLASS_LABELS)
df[TARGET_COLUMN] = le_target.transform(df[TARGET_COLUMN].astype(str))

X = df[FEATURE_COLUMNS].values.astype(float)
y = df[TARGET_COLUMN].values

class_counts = pd.Series(y).value_counts()
min_class_count = class_counts.min() if len(class_counts) > 0 else 0
can_stratify = (len(class_counts) >= 2) and (min_class_count >= 2)
stratify_param = y if can_stratify else None

if len(df) < 5:
    X_train, y_train = X, y
    X_test, y_test = X, y
else:
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42, stratify=stratify_param
    )

model = GaussianNB()
model.fit(X_train, y_train)

cv_score = None
try:
    train_class_counts = pd.Series(y_train).value_counts()
    min_train_class_cnt = train_class_counts.min() if len(train_class_counts) > 0 else 0
    
    if len(X_train) >= 4 and len(train_class_counts) >= 2 and min_train_class_cnt >= 2:
        n_splits = min(5, min_train_class_cnt, len(X_train))
        if n_splits >= 2:
            cv_scores = cross_val_score(model, X_train, y_train, cv=n_splits, scoring='accuracy')
            cv_score = float(np.mean(cv_scores))
    elif len(X_train) >= 4:
        from sklearn.model_selection import KFold
        n_splits = min(5, len(X_train))
        kf = KFold(n_splits=n_splits, shuffle=True, random_state=42)
        cv_scores = cross_val_score(model, X_train, y_train, cv=kf, scoring='accuracy')
        cv_score = float(np.mean(cv_scores))
except Exception:
    cv_score = None

# round to percentage with 1 decimal
cv_pct = round(cv_score * 100, 1) if cv_score is not None else None

print(json.dumps({'cv_score': cv_score, 'cv_percent': cv_pct}))

