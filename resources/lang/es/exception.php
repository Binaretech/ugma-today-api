<?php

return [
    'internal_error' => 'Ha ocurrido un error.',
    'unauthenticated' => 'No autenticado.',
    'login' => 'Usuario o contraseña incorrecta.',
    'not_found' => ':resource no encontrado',
    'invalid_data' => 'Los datos proporcionados no son válidos.',
    'invalid_user_rights' => 'El usuario no cuenta con permisos apropiados.',
    'weak_password' => 'La contraseña introducida no está permitida',
    'username_used' => 'El valor del campo username ya está en uso',
    'email_used' => 'El valor del campo email ya está en uso',
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
        'already_unliked' => 'Ya le ha indicado que le no gusta esta publicación',
        'error_saving' => 'Hubo un error al guardar la publicación, intente nuevamente',
    ],
    'CommentController' => [
        'store' => 'No se pudo guardar el comentario, intente de nuevo.',
        'reply' => 'No puede responder a un comentario de respuesta',
        'already_liked' => 'Ya le ha indicado que le gusta este comentario',
        'already_unliked' => 'Ya le ha indicado que le no gusta este comentario',
    ]
];
