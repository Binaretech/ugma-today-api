from django.contrib.auth.models import AbstractBaseUser
from django.db import models

class User(AbstractBaseUser):
    username = models.CharField(max_length=100, unique=True)
    status = models.BooleanField()
    type = models.PositiveSmallIntegerField()
    
    USERNAME_FIELD = 'username'    
