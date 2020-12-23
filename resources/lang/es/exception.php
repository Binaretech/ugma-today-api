<?php

return [
	'internal_error' => 'Ha ocurrido un error.',
	'unauthenticated' => 'No autenticado.',
	'login' => 'Usuario o contraseña incorrecta.',
	'not_found' => ':resource no encontrado',
	'invalid_data' => 'Los datos proporcionados no son válidos.',
	'resource' => [
		'App\\Models\\User' => 'Usuario no encontrado',
		'App\\Models\\Cost' => 'Costo no encotrado',
		'App\\Models\\Post' => 'Publicacion no encontrada',
		'App\\Models\\Comment' => 'Comentario no encontrado',
	],
	'PostController' => [
		'not_found_news' => 'Noticia no encontrada',
		'not_found_post' => 'Publicación no encontrada',
		'already_liked' => 'Ya le ha indicado que le gusta esta publicación',
	],
	'CommentController' => [
		'store' => 'No se pudo guardar el comentario, intente de nuevo.',
		'reply' => 'No puede responder a un comentario de respuesta',
	]
];
