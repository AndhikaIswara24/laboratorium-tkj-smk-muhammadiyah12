"""
Configuration for Flask Naive Bayes API.
Reads database credentials from the project .env file (same as Laravel).
"""

import os

def load_env(env_path):
    """Parse a .env file and return a dict of key-value pairs."""
    env_vars = {}
    if not os.path.exists(env_path):
        return env_vars
    with open(env_path, 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if not line or line.startswith('#'):
                continue
            if '=' not in line:
                continue
            key, value = line.split('=', 1)
            key = key.strip()
            value = value.strip().strip('"').strip("'")
            env_vars[key] = value
    return env_vars


# Load .env from project root (one level up from flask_api/)
_env_path = os.path.join(os.path.dirname(__file__), '..', '.env')
_env = load_env(_env_path)

def get_env(key, default=''):
    """Get environment variable from system os.environ first, falling back to parsed .env file."""
    val = os.environ.get(key)
    if val is not None and val != '':
        return val
    return _env.get(key, default)

# Database configuration
DB_CONFIG = {
    'host': get_env('DB_HOST', '127.0.0.1'),
    'port': int(get_env('DB_PORT', '3306')),
    'database': get_env('DB_DATABASE', 'db_inventaris_lab_tkj'),
    'user': get_env('DB_USERNAME', 'root'),
    'password': get_env('DB_PASSWORD', ''),
}

# Model file path
MODEL_DIR = os.path.join(os.path.dirname(__file__), 'model')
MODEL_PATH = os.path.join(MODEL_DIR, 'naive_bayes_model.pkl')

# Flask settings
FLASK_HOST = '127.0.0.1'
FLASK_PORT = 5000
FLASK_DEBUG = str(get_env('APP_DEBUG', 'false')).lower() == 'true'

# API key used to authenticate requests from Laravel to this Flask API
FLASK_API_KEY = get_env('FLASK_API_KEY', '')

# CORS: allowed origin for requests (set to your Laravel production origin)
# Example: https://app.example.com
CORS_ALLOWED_ORIGIN = get_env('CORS_ALLOWED_ORIGIN', 'http://127.0.0.1:8000')
