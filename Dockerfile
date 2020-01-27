FROM python:3.7-alpine

RUN apk add git
COPY requirements.txt /app/
WORKDIR /app
RUN pip install -r requirements.txt