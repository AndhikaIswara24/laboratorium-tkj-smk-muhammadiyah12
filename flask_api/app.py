"""
Flask REST API for Naive Bayes Prediction.

Endpoints:
    POST /train   — Train GaussianNB on t_naive_bayes_dataset and save model
    POST /predict — Load model and return predicted class + probabilities
    GET  /health  — Health check
"""

import os
import json
import traceback

import joblib
import numpy as np
import pandas as pd
import mysql.connector
from flask import Flask, request, jsonify
from sklearn.naive_bayes import GaussianNB
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import cross_val_score, train_test_split
from sklearn.metrics import classification_report, accuracy_score, confusion_matrix

from config import DB_CONFIG, MODEL_DIR, MODEL_PATH, FLASK_HOST, FLASK_PORT, FLASK_DEBUG, FLASK_API_KEY, CORS_ALLOWED_ORIGIN

app = Flask(__name__)


# ---------------------------------------------------------------------------
# Security: require API key for modifying endpoints and apply CORS policy
# ---------------------------------------------------------------------------
@app.before_request
def require_api_key():
    # Allow unauthenticated health check
    if request.method == 'GET' and request.path == '/health':
        return None

    # Expect API key in header 'X-API-KEY' or query param 'api_key'
    incoming = request.headers.get('X-API-KEY') or request.args.get('api_key')
    if not FLASK_API_KEY:
        # No API key configured: deny in production by default
        return jsonify({'success': False, 'message': 'API key not configured on server.'}), 403

    if not incoming or incoming != FLASK_API_KEY:
        return jsonify({'success': False, 'message': 'Unauthorized. Missing or invalid API key.'}), 401


@app.after_request
def apply_cors(response):
    origin = request.headers.get('Origin')
    allowed = CORS_ALLOWED_ORIGIN
    # If allowed origin is wildcard in dev, reflect request origin; in prod set explicit origin
    if allowed == '*' and origin:
        response.headers['Access-Control-Allow-Origin'] = origin
    else:
        response.headers['Access-Control-Allow-Origin'] = allowed
    response.headers['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS'
    response.headers['Access-Control-Allow-Headers'] = 'Content-Type, X-API-KEY'
    return response

# ---------------------------------------------------------------------------
# Feature definitions
# ---------------------------------------------------------------------------
# All 10 feature columns in the order they appear in t_naive_bayes_dataset
FEATURE_COLUMNS = [
    'kondisi_brg',
    'usia_pakai',
    'frq_kerusakan',
    'jenis_pm',
    'interval_pm',
    'efi_out',
    'downtime',
    'lingkungan',
    'daya_listrik',
    'sparepart',
]

# Categorical columns that need label-encoding
CATEGORICAL_COLUMNS = [
    'kondisi_brg',
    'jenis_pm',
    'efi_out',
    'lingkungan',
    'daya_listrik',
    'sparepart',
]

# Numeric columns (kept as-is)
NUMERIC_COLUMNS = [
    'usia_pakai',
    'frq_kerusakan',
    'interval_pm',
    'downtime',
]

# Target column
TARGET_COLUMN = 'kelas_label'

# Class labels in a fixed order so probabilities always map correctly
CLASS_LABELS = ['Layak', 'Perlu Servis', 'Tidak Layak']


# ---------------------------------------------------------------------------
# Helper: database connection
# ---------------------------------------------------------------------------
def get_db_connection():
    """Return a new MySQL connection using the project .env credentials."""
    return mysql.connector.connect(**DB_CONFIG)


# ---------------------------------------------------------------------------
# TRAIN endpoint
# ---------------------------------------------------------------------------
@app.route('/train', methods=['POST'])
def train_model():
    """
    Read t_naive_bayes_dataset from MySQL, label-encode categorical features,
    train a GaussianNB model, evaluate it, and save to disk as .pkl.
    """
    try:
        # 1. Baca dataset dari Mysql
        conn = get_db_connection()
        cursor = conn.cursor(dictionary=True)
        columns = FEATURE_COLUMNS + [TARGET_COLUMN]
        query = f"SELECT {', '.join(columns)} FROM t_naive_bayes_dataset WHERE {TARGET_COLUMN} IS NOT NULL"
        cursor.execute(query)
        rows = cursor.fetchall()
        cursor.close()
        conn.close()
        df = pd.DataFrame(rows, columns=columns)

        if df.empty or len(df) < 2:
            return jsonify({
                'success': False,
                'message': 'Dataset terlalu sedikit (minimal 2 data aset). Silakan generate atau tambahkan dataset terlebih dahulu.',
            }), 400

        # 2. Label-encode kategorikal 
        encoders = {}
        for col in CATEGORICAL_COLUMNS:
            le = LabelEncoder()
            df[col] = le.fit_transform(df[col].astype(str))
            encoders[col] = le

        # Encode label target
        le_target = LabelEncoder()
        le_target.fit(CLASS_LABELS)  # fixed order
        df[TARGET_COLUMN] = le_target.transform(df[TARGET_COLUMN].astype(str))
        encoders[TARGET_COLUMN] = le_target

        # 3. Pembagian data (80% train, 20% test)
        X = df[FEATURE_COLUMNS].values.astype(float)
        y = df[TARGET_COLUMN].values

        class_counts = pd.Series(y).value_counts()
        min_class_count = class_counts.min() if len(class_counts) > 0 else 0
        # Stratifikasi memerlukan setidaknya 2 sampel pada setiap kelas
        can_stratify = (len(class_counts) >= 2) and (min_class_count >= 2)
        stratify_param = y if can_stratify else None

        if len(df) < 5:
            X_train, y_train = X, y
            X_test, y_test = X, y
        else:
            X_train, X_test, y_train, y_test = train_test_split(
                X, y, test_size=0.2, random_state=42, stratify=stratify_param
            )

        # 4. Model Naive Bayes — latih hanya pada data train
        model = GaussianNB()
        model.fit(X_train, y_train)

        # Evaluasi pada data latih dan data uji
        y_train_pred = model.predict(X_train)
        y_test_pred = model.predict(X_test) if len(X_test) > 0 else y_train_pred

        train_accuracy = accuracy_score(y_train, y_train_pred)
        test_accuracy = accuracy_score(y_test, y_test_pred) if len(X_test) > 0 else train_accuracy

        # Evaluasi pada seluruh dataset (501 data) agar kelas minoritas (Layak, Perlu Servis, Tidak Layak) terevaluasi secara utuh
        y_all_pred = model.predict(X)
        all_accuracy = accuracy_score(y, y_all_pred)

        # Cross-validation on training set safely
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

        present_labels = sorted(np.unique(y).tolist())
        present_names = [le_target.inverse_transform([c])[0] for c in present_labels]

        # Overall classification report and confusion matrix (seluruh dataset)
        report_all = classification_report(
            y,
            y_all_pred,
            labels=present_labels,
            target_names=present_names,
            output_dict=True,
            zero_division=0,
        )
        cm_all = confusion_matrix(y, y_all_pred, labels=present_labels)

        # Test set classification report and confusion matrix
        report_test = classification_report(
            y_test,
            y_test_pred,
            labels=present_labels,
            target_names=present_names,
            output_dict=True,
            zero_division=0,
        )
        cm_test = confusion_matrix(y_test, y_test_pred, labels=present_labels)

        # 6. Simpan model + encoders
        os.makedirs(MODEL_DIR, exist_ok=True)
        artifact = {
            'model': model,
            'encoders': encoders,
            'feature_columns': FEATURE_COLUMNS,
            'categorical_columns': CATEGORICAL_COLUMNS,
            'class_labels': CLASS_LABELS,
        }
        joblib.dump(artifact, MODEL_PATH)

        return jsonify({
            'success': True,
            'message': f'Model berhasil dilatih dengan {len(df)} data.',
            'total_data': len(df),
            'train_data': len(X_train),
            'test_data': len(X_test),
            'train_accuracy': round(train_accuracy, 4),
            'test_accuracy': round(test_accuracy, 4),
            'accuracy': round(all_accuracy, 4),
            'cv_accuracy': round(cv_score, 4) if cv_score is not None else None,
            'classification_report': report_all,
            'classification_report_test': report_test,
            'confusion_matrix': cm_all.tolist(),
            'confusion_matrix_test': cm_test.tolist(),
            'class_labels': present_names,
        })

    except Exception as e:
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Gagal melatih model: {str(e)}',
        }), 500


# ---------------------------------------------------------------------------
# PREDICT endpoint
# ---------------------------------------------------------------------------
@app.route('/predict', methods=['POST'])
def predict():
    """
    Accept JSON with the 10 feature values, load the saved model,
    and return the predicted class plus 3-class probabilities.

    Expected JSON body:
    {
        "kondisi_brg": "B",
        "usia_pakai": 3,
        "frq_kerusakan": 1,
        "jenis_pm": "Preventif",
        "interval_pm": 6,
        "efi_out": "Tinggi",
        "downtime": 2.5,
        "lingkungan": "Baik",
        "daya_listrik": "Stabil",
        "sparepart": "Tersedia"
    }
    """
    try:
        # 1. Validate model exists -------------------------------------------
        if not os.path.exists(MODEL_PATH):
            return jsonify({
                'success': False,
                'message': 'Model belum dilatih. Silakan panggil /train terlebih dahulu.',
            }), 400

        # 2. Parse input -----------------------------------------------------
        input_data = request.get_json(force=True)
        if not input_data:
            return jsonify({
                'success': False,
                'message': 'Input data tidak boleh kosong.',
            }), 400

        # Validate all required features are present
        missing = [col for col in FEATURE_COLUMNS if col not in input_data]
        if missing:
            return jsonify({
                'success': False,
                'message': f'Fitur berikut belum diisi: {", ".join(missing)}',
            }), 400

        # 3. Load model ------------------------------------------------------
        artifact = joblib.load(MODEL_PATH)
        model = artifact['model']
        encoders = artifact['encoders']
        class_labels = artifact['class_labels']

        # 4. Encode input features -------------------------------------------
        row = []
        for col in FEATURE_COLUMNS:
            val = input_data[col]
            if col in CATEGORICAL_COLUMNS:
                le = encoders[col]
                # Handle unseen labels gracefully
                if str(val) not in le.classes_:
                    return jsonify({
                        'success': False,
                        'message': f'Nilai "{val}" tidak dikenali untuk fitur "{col}". '
                                   f'Nilai yang dikenali: {list(le.classes_)}',
                    }), 400
                val = le.transform([str(val)])[0]
            row.append(float(val))

        X_input = np.array([row])

        # 5. Predict ---------------------------------------------------------
        prediction_encoded = model.predict(X_input)[0]
        probabilities = model.predict_proba(X_input)[0]

        le_target = encoders[TARGET_COLUMN]
        predicted_class = le_target.inverse_transform([prediction_encoded])[0]

        # Map probabilities to class labels
        # model.classes_ gives the encoded classes in order
        prob_dict = {}
        for encoded_class, prob in zip(model.classes_, probabilities):
            label = le_target.inverse_transform([encoded_class])[0]
            prob_dict[label] = round(float(prob), 6)

        return jsonify({
            'success': True,
            'predicted_class': predicted_class,
            'probabilities': {
                'Layak': prob_dict.get('Layak', 0.0),
                'Perlu Servis': prob_dict.get('Perlu Servis', 0.0),
                'Tidak Layak': prob_dict.get('Tidak Layak', 0.0),
            },
        })

    except Exception as e:
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Gagal melakukan prediksi: {str(e)}',
        }), 500


# ---------------------------------------------------------------------------
# BATCH PREDICT endpoint
# ---------------------------------------------------------------------------
@app.route('/predict_batch', methods=['POST'])
def predict_batch():
    """
    Accept JSON array of objects, load saved model, and return predicted classes + probabilities for all records.
    """
    try:
        if not os.path.exists(MODEL_PATH):
            return jsonify({
                'success': False,
                'message': 'Model belum dilatih. Silakan panggil /train terlebih dahulu.',
            }), 400

        input_list = request.get_json(force=True)
        if not input_list or not isinstance(input_list, list):
            return jsonify({
                'success': False,
                'message': 'Input data harus berupa array/list JSON.',
            }), 400

        artifact = joblib.load(MODEL_PATH)
        model = artifact['model']
        encoders = artifact['encoders']
        class_labels = artifact['class_labels']
        le_target = encoders[TARGET_COLUMN]

        rows = []
        for idx, item in enumerate(input_list):
            missing = [col for col in FEATURE_COLUMNS if col not in item]
            if missing:
                return jsonify({
                    'success': False,
                    'message': f'Baris indeks {idx} kekurangan fitur: {", ".join(missing)}',
                }), 400

            row = []
            for col in FEATURE_COLUMNS:
                val = item[col]
                if col in CATEGORICAL_COLUMNS:
                    le = encoders[col]
                    val_str = str(val)
                    if val_str not in le.classes_:
                        val_str = le.classes_[0]
                    val = le.transform([val_str])[0]
                row.append(float(val))
            rows.append(row)

        X_input = np.array(rows, dtype=float)

        predictions_encoded = model.predict(X_input)
        probabilities_list = model.predict_proba(X_input)

        results = []
        for idx, item in enumerate(input_list):
            pred_encoded = predictions_encoded[idx]
            probs = probabilities_list[idx]
            predicted_class = le_target.inverse_transform([pred_encoded])[0]

            prob_dict = {}
            for encoded_class, prob in zip(model.classes_, probs):
                label = le_target.inverse_transform([encoded_class])[0]
                prob_dict[label] = round(float(prob), 6)

            results.append({
                'id_dataset': item.get('id_dataset', 0),
                'id_aset': item.get('id_aset', 0),
                'predicted_class': predicted_class,
                'probabilities': {
                    'Layak': prob_dict.get('Layak', 0.0),
                    'Perlu Servis': prob_dict.get('Perlu Servis', 0.0),
                    'Tidak Layak': prob_dict.get('Tidak Layak', 0.0),
                }
            })

        return jsonify({
            'success': True,
            'results': results
        })

    except Exception as e:
        traceback.print_exc()
        return jsonify({
            'success': False,
            'message': f'Gagal melakukan batch prediksi: {str(e)}',
        }), 500


# ---------------------------------------------------------------------------
# HEALTH endpoint
# ---------------------------------------------------------------------------
@app.route('/health', methods=['GET'])
def health():
    """Health check — also reports whether a trained model is available."""
    model_exists = os.path.exists(MODEL_PATH)
    return jsonify({
        'status': 'ok',
        'model_ready': model_exists,
    })


# ---------------------------------------------------------------------------
# Run
# ---------------------------------------------------------------------------
if __name__ == '__main__':
    # In production use gunicorn/uvicorn or systemd to run the Flask app; the built-in server is for debug only
    app.run(host=FLASK_HOST, port=FLASK_PORT, debug=FLASK_DEBUG)
