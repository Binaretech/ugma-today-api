from django.db import models
from django.contrib.auth.models import AbstractUser


class User(AbstractUser):
    email = None
    first_name = None
    last_name = None


class Profile(models.Model):
    id = None
    name = models.CharField(max_length=50)
    lastname = models.CharField(max_length=50)
    email = models.EmailField()
    user = models.OneToOneField(User, models.CASCADE, primary_key=True)


class Post(models.Model):
    user = models.ForeignKey(User, models.SET_NULL)
    title = models.CharField(max_length=128)
    content = models.TextField()
