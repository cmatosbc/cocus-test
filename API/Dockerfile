FROM python:3.11-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

# Create a non-root user and switch to it
RUN useradd -m -r -s /bin/bash appuser && \
    chown -R appuser:appuser /app

USER appuser

ENV FLASK_APP=app.py
ENV PYTHONUNBUFFERED=1

CMD ["flask", "run", "--host=0.0.0.0"]
