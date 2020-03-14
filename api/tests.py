from django.test import TestCase
from rest_framework import status
from rest_framework.test import APIClient
import random
import string


def randomString(stringLength=10):
    letters = string.ascii_lowercase
    return ''.join(random.choice(letters) for i in range(stringLength))


class AuthViewTests(TestCase):
    def test_register(self):
        response = self.client.post('/api/register/', data={
            'username': 'test_user',
            'password': 'test_password',
            'profile': {
                'name': randomString(),
                'lastname': randomString(),
                'email': randomString()+'@'+randomString()+'.com'
            }
        }, content_type='application/json')

        self.assertEqual(response.status_code, 201)

    def test_login(self):
        response = self.client.post('/api/register/', data={
            'username': 'test_user',
            'password': 'test_password',
            'profile': {
                'name': randomString(),
                'lastname': randomString(),
                'email': randomString()+'@'+randomString()+'.com'
            }
        }, content_type='application/json')

        self.assertEqual(response.status_code, status.HTTP_201_CREATED)

        response = self.client.post('/api/login/', data={
            'username': 'test_user',
            'password': 'test_password',
        })

        self.assertEqual(response.status_code, status.HTTP_200_OK)


class PostViewSetTests(TestCase):
    def setUp(self):
        self.client = APIClient()
        response = self.client.post('/api/register/', data={
            'username': 'test_user',
            'password': 'test_password',
            'profile': {
                'name': randomString(),
                'lastname': randomString(),
                'email': randomString()+'@'+randomString()+'.com'
            }
        }, format='json')

        self.client.credentials(
            HTTP_AUTHORIZATION='Token ' + response.data['token'])

    def test_index(self):
        response = self.client.get('/api/post/')
        self.assertEqual(response.status_code,
                         status.HTTP_200_OK)

    def test_store(self):
        data = {
            'title': randomString(),
            'content': randomString(),
        }
        response = self.client.post('/api/post/', data, format='json')
        self.assertEqual(response.status_code, status.HTTP_201_CREATED)

    def test_show_not_found(self):
        response = self.client.get('/api/post/1/')
        self.assertEqual(response.status_code, status.HTTP_404_NOT_FOUND)

    def test_show_not_found(self):
        response = self.client.get('/api/post/1/')
        self.assertEqual(response.status_code, status.HTTP_404_NOT_FOUND)

    def test_show(self):
        data = {
            'title': randomString(),
            'content': randomString(),
        }
        response = self.client.post('/api/post/', data, format='json')
        self.assertEqual(response.status_code, status.HTTP_201_CREATED)

        response = self.client.get('/api/post/1/')
        self.assertEqual(response.status_code, status.HTTP_404_NOT_FOUND)

    def test_delete(self):
        data = {
            'title': randomString(),
            'content': randomString(),
        }
        response = self.client.post('/api/post/', data, format='json')
        self.assertEqual(response.status_code, status.HTTP_201_CREATED)

        response = self.client.delete('/api/post/1/')
        self.assertEqual(response.status_code, status.HTTP_200_OK)


class CommentViewSetTests(TestCase):
    def setUp(self):
        self.client = APIClient()
        response = self.client.post('/api/register/', data={
            'username': 'test_user',
            'password': 'test_password',
            'profile': {
                'name': randomString(),
                'lastname': randomString(),
                'email': randomString()+'@'+randomString()+'.com'
            }
        }, format='json')

        self.client.credentials(
            HTTP_AUTHORIZATION='Token ' + response.data['token'])
