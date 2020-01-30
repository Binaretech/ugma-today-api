FROM python:3.7

RUN apt-get install git libpq postgresql-dev gcc
COPY requirements.txt /app/

WORKDIR /app

RUN pip install --upgrade pip

RUN pip install -r requirements.txt