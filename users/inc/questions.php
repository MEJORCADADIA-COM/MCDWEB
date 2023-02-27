<?php

$questions = [
    'most_excited' => '¿Qué es lo que Más te Entusiasma en tu Vida Ahora?',
    'deep_commitments' => 'Tres Compromisos Profundos que tienes en tu Vida ahora:',
    'biggest_dreams' => '¿Cuál son tus 3-Sueños Más Grandes, importantes o locos?',
    'like' => '¿Qué es lo que Más te Gusta Hacer? ',
    'promise_achievement' => 'Me Prometo a mi mismo/a Hacer  o lograr esto:',
    'let_not_happen' => 'No Voy a Permitir que Suceda esto:',
    'do_before_death' => 'Si sólo te quedase 1-Año de Vida, ¿Qué harías? ',
    'how_help_humanity' => 'Si tuvieras 1Billón de dólares, ¿Cómo los usarías para Ayudar a la Humanidad?',
    'legacy' => '¿Cómo podrías dejar un Legado que dure 1000 años?',
    'best_in_the_world' => '¿En qué quieres ser el/la Mejor del Mundo?'
];

function prepareAnswers($answers) {
    global $questions;
    $data = [];
    foreach ($questions as $key => $question) {
        if (!empty($answers[$key])) {
            $data[$key] = $answers[$key];
        }
    }
    return $data;
}