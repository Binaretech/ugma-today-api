from django.test import TestCase
from django.urls import reverse
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

        self.assertEqual(response.status_code, 201)

        response = self.client.post('/api/login/', data={
            'username': 'test_user',
            'password': 'test_password',
        })

        self.assertEqual(response.status_code, 200, response.data)
