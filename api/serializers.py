from rest_framework import serializers
from .models import User, Profile, Post
from django.db import transaction


class ProfileSerializer(serializers.ModelSerializer):
    class Meta:
        model = Profile
        exclude = ['user']


class UserSerializer(serializers.ModelSerializer):
    profile = ProfileSerializer(many=False)

    class Meta:
        model = User
        fields = '__all__'

    password = serializers.CharField(write_only=True)

    def create(self, validated_data):
        with transaction.atomic():
            user = User.objects.create(username=validated_data['username'])
            user.set_password(validated_data['password'])
            user.save()
            Profile.objects.create(user=user, **validated_data['profile'])

        return user


class PostSerializer(serializers.ModelSerializer):
    class Meta:
        model = Post
        exclude = ['user']
