import os
from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
from dotenv import load_dotenv
from datetime import datetime

# Determine the environment and load the appropriate .env file
environment = os.getenv('FLASK_ENV', 'production')
if environment == 'test':
    load_dotenv('.env.test')
elif environment == 'development':
    load_dotenv('.env.dev')
else:
    load_dotenv()  # Production .env

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Enable debug logging
app.logger.setLevel('DEBUG')

# Database Configuration
app.config['SQLALCHEMY_DATABASE_URI'] = os.getenv('DATABASE_URL')
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

db = SQLAlchemy(app)

# Models
class User(db.Model):
    __tablename__ = 'user'  # Match Symfony's table name
    
    id = db.Column(db.Integer, primary_key=True)
    email = db.Column(db.String(180), unique=True, nullable=False)
    roles = db.Column(db.JSON, nullable=False)
    password = db.Column(db.String(255), nullable=False)
    name = db.Column(db.String(255), nullable=False)
    created_at = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, nullable=False, default=datetime.utcnow, onupdate=datetime.utcnow)
    notes = db.relationship('Note', backref='user', lazy=True)

class Note(db.Model):
    __tablename__ = 'note'  # Match Symfony's table name
    
    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    message = db.Column(db.String(255), nullable=False)
    type = db.Column(db.Integer, nullable=False)  # 0: Info, 1: Warning, 2: Critical
    created_at = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, nullable=False, default=datetime.utcnow, onupdate=datetime.utcnow)
    user_id = db.Column(db.Integer, db.ForeignKey('user.id'), nullable=False)

# Routes
@app.route('/python_api/notes', methods=['GET'])
def get_notes():
    notes = Note.query.all()
    return jsonify([{
        'id': note.id,
        'title': note.title,
        'message': note.message,
        'type': note.type,
        'created_at': note.created_at.isoformat(),
        'updated_at': note.updated_at.isoformat(),
        'user_id': note.user_id
    } for note in notes])

@app.route('/python_api/notes/<int:note_id>', methods=['GET'])
def get_note(note_id):
    note = Note.query.get_or_404(note_id)
    return jsonify({
        'id': note.id,
        'title': note.title,
        'message': note.message,
        'type': note.type,
        'created_at': note.created_at.isoformat(),
        'updated_at': note.updated_at.isoformat(),
        'user_id': note.user_id
    })

@app.route('/python_api/notes', methods=['POST'])
def create_note():
    app.logger.debug('Received POST request to /python_api/notes')
    try:
        data = request.get_json()
        app.logger.debug(f'Received data: {data}')
        
        if not all(k in data for k in ('title', 'message', 'type', 'user_id')):
            app.logger.error('Missing required fields')
            return jsonify({'error': 'Missing required fields'}), 400
        
        note = Note(
            title=data['title'],
            message=data['message'],
            type=data['type'],
            user_id=data['user_id']
        )
        
        db.session.add(note)
        db.session.commit()
        
        response = {
            'id': note.id,
            'title': note.title,
            'message': note.message,
            'type': note.type,
            'created_at': note.created_at.isoformat(),
            'updated_at': note.updated_at.isoformat(),
            'user_id': note.user_id
        }
        app.logger.debug(f'Created note: {response}')
        return jsonify(response), 201
    except Exception as e:
        app.logger.error(f'Error creating note: {str(e)}')
        return jsonify({'error': str(e)}), 500

@app.route('/python_api/notes/<int:note_id>', methods=['PUT'])
def update_note(note_id):
    note = Note.query.get_or_404(note_id)
    data = request.get_json()
    
    if not all(k in data for k in ('title', 'message', 'type')):
        return jsonify({'error': 'Missing required fields'}), 400
    
    note.title = data['title']
    note.message = data['message']
    note.type = data['type']
    
    db.session.commit()
    
    return jsonify({
        'id': note.id,
        'title': note.title,
        'message': note.message,
        'type': note.type,
        'created_at': note.created_at.isoformat(),
        'updated_at': note.updated_at.isoformat(),
        'user_id': note.user_id
    })

@app.route('/python_api/notes/<int:note_id>', methods=['DELETE'])
def delete_note(note_id):
    note = Note.query.get_or_404(note_id)
    db.session.delete(note)
    db.session.commit()
    return '', 204

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
    app.run(host='0.0.0.0', debug=True)
