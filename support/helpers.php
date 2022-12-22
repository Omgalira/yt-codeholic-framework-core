<?php

function csrf_field(): string
{
    return sprintf('<input type="hidden" name="_token" value="%s">', csrf_token());
}

function csrf_token(): string
{
    // TODO: Obtener la sesión (de preferencia un objeto). Si existe, retornar el token. Ej. $session->token().
    $session = ['token' => uniqid()];

    if (isset($session)) {
        return $session['token'];
    }

    // TODO: Si no, generar una excepción. Mensaje: Application session store not set.
}

function method_field($method): string
{
    return sprintf('<input type="hidden" name="_method" value="%s">', $method);
}
