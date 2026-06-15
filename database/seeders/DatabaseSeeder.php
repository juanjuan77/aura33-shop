<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(['email' => 'admin@aura33.com'], [
            'name'     => 'Admin AURA33',
            'password' => bcrypt('aura33admin'),
        ]);

        // Categorías
        $botellas = Category::create([
            'name'        => 'Botellas de Cristal',
            'slug'        => 'botellas-de-cristal',
            'description' => 'Botellas de vidrio con cristales naturales en su interior. Cargá tu agua con la energía de cada piedra.',
            'icon'        => '💧',
            'sort_order'  => 1,
        ]);

        $torres = Category::create([
            'name'        => 'Torres de Cristal',
            'slug'        => 'torres-de-cristal',
            'description' => 'Puntas de cristal natural para decoración, meditación y trabajo energético.',
            'icon'        => '🔮',
            'sort_order'  => 2,
        ]);

        $oraculos = Category::create([
            'name'        => 'Oráculos y Tarot',
            'slug'        => 'oraculos-y-tarot',
            'description' => 'Barajas de oráculo y tarot para tu camino espiritual.',
            'icon'        => '✨',
            'sort_order'  => 3,
        ]);

        // Productos: Botellas de Cristal
        $cristales_botellas = [
            [
                'name'             => 'Botella Amatista',
                'slug'             => 'botella-amatista',
                'short_description'=> 'Calma, protección y conexión espiritual.',
                'description'      => 'La amatista es la piedra de la espiritualidad y la calma. Perfecta para meditar, limpiar energías negativas y conectar con tu intuición. Ideal para quienes buscan paz interior.',
                'properties'       => [
                    'chakra'     => 'Corona',
                    'beneficios' => ['Calma la mente', 'Favorece la meditación', 'Protección energética', 'Trabaja el chacra corona', 'Alivia el estrés y la ansiedad', 'Potencia la intuición'],
                    'combina_con'=> ['Cuarzo Rosa', 'Lapislázuli'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 15,
                'featured'         => true,
            ],
            [
                'name'             => 'Botella Cuarzo Rosa',
                'slug'             => 'botella-cuarzo-rosa',
                'short_description'=> 'Amor, autoestima y armonía.',
                'description'      => 'El cuarzo rosa es la piedra del amor universal. Atrae el amor romántico, fomenta el amor propio y armoniza las relaciones. Trabaja el chakra cardíaco.',
                'properties'       => [
                    'chakra'     => 'Corazón',
                    'beneficios' => ['Atrae el amor', 'Autoestima y amor propio', 'Armoniza relaciones', 'Trabaja el chacra cardíaco', 'Reduce tensiones emocionales', 'Fomenta la compasión'],
                    'combina_con'=> ['Amatista', 'Cuarzo Blanco'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 20,
                'featured'         => true,
            ],
            [
                'name'             => 'Botella Ojo de Tigre',
                'slug'             => 'botella-ojo-de-tigre',
                'short_description'=> 'Abundancia, prosperidad y confianza.',
                'description'      => 'El ojo de tigre es la piedra de la abundancia y el éxito. Atrae prosperidad, fortalece la confianza y ayuda en la toma de decisiones. Excelente para exámenes y entrevistas laborales.',
                'properties'       => [
                    'chakra'     => 'Plexo Solar',
                    'beneficios' => ['Atrae abundancia y prosperidad', 'Barrera de malas energías', 'Confianza y autoestima', 'Ayuda en toma de decisiones', 'Excelente para exámenes o entrevistas', 'Ayuda para tener ideas claras'],
                    'combina_con'=> ['Pirita', 'Citrino'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 12,
                'featured'         => true,
            ],
            [
                'name'             => 'Botella Citrino',
                'slug'             => 'botella-citrino',
                'short_description'=> 'Éxito, abundancia y energía solar.',
                'description'      => 'El citrino es conocido como la "piedra del comerciante". Atrae el éxito económico, llena de energía positiva y estimula la creatividad. No necesita limpieza energética.',
                'properties'       => [
                    'chakra'     => 'Plexo Solar',
                    'beneficios' => ['Atrae éxito y abundancia', 'Energía positiva', 'Estimula la creatividad', 'No necesita limpieza', 'Ideal para negocios', 'Combate la depresión'],
                    'combina_con'=> ['Ojo de Tigre', 'Pirita'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 10,
                'featured'         => false,
            ],
            [
                'name'             => 'Botella Lapislázuli',
                'slug'             => 'botella-lapislazuli',
                'short_description'=> 'Comunicación, verdad e intuición.',
                'description'      => 'El lapislázuli estimula la sabiduría y la verdad. Mejora la comunicación, desarrolla la intuición y fortalece la mente. Ha sido valorado desde el Antiguo Egipto como piedra de reyes.',
                'properties'       => [
                    'chakra'     => 'Tercer Ojo / Garganta',
                    'beneficios' => ['Estimula la sabiduría', 'Mejora la comunicación', 'Desarrolla la intuición', 'Conexión espiritual', 'Reduce el estrés', 'Potencia la memoria'],
                    'combina_con'=> ['Amatista', 'Cuarzo Blanco'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 8,
                'featured'         => false,
            ],
            [
                'name'             => 'Botella Obsidiana',
                'slug'             => 'botella-obsidiana',
                'short_description'=> 'Protección intensa y limpieza profunda.',
                'description'      => 'La obsidiana es el escudo energético más potente. Absorbe y transforma las energías negativas, protege de personas tóxicas y ayuda a sanar heridas emocionales del pasado.',
                'properties'       => [
                    'chakra'     => 'Raíz',
                    'beneficios' => ['Protección intensa', 'Absorbe energías negativas', 'Limpieza profunda del aura', 'Sana heridas emocionales', 'Conexión a tierra', 'Aleja personas tóxicas'],
                    'combina_con'=> ['Turmalina Negra', 'Hematita'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 10,
                'featured'         => false,
            ],
            [
                'name'             => 'Botella Fluorita Verde',
                'slug'             => 'botella-fluorita-verde',
                'short_description'=> 'Equilibrio, concentración y claridad mental.',
                'description'      => 'La fluorita verde organiza el pensamiento y mejora la concentración. Es ideal para el estudio, el trabajo y para tomar decisiones con claridad. Limpia el aura y equilibra los chakras.',
                'properties'       => [
                    'chakra'     => 'Corazón',
                    'beneficios' => ['Concentración y estudio', 'Claridad mental', 'Equilibrio emocional', 'Limpia el aura', 'Ideal para trabajar', 'Toma de decisiones'],
                    'combina_con'=> ['Cuarzo Blanco', 'Amatista'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 7,
                'featured'         => false,
            ],
            [
                'name'             => 'Botella Labradorita',
                'slug'             => 'botella-labradorita',
                'short_description'=> 'Magia, transformación y protección espiritual.',
                'description'      => 'La labradorita es la piedra de la magia y los cambios. Protege el aura, estimula la intuición y acompaña en momentos de transformación personal. Sus destellos iridiscentes la hacen única.',
                'properties'       => [
                    'chakra'     => 'Tercer Ojo',
                    'beneficios' => ['Protección del aura', 'Estimula la intuición', 'Acompaña en transformaciones', 'Magia y misticismo', 'Clarividencia', 'Aleja energías oscuras'],
                    'combina_con'=> ['Amatista', 'Piedra Luna'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 6,
                'featured'         => false,
            ],
            [
                'name'             => 'Botella Sodalita',
                'slug'             => 'botella-sodalita',
                'short_description'=> 'Calma, lógica y comunicación efectiva.',
                'description'      => 'La sodalita combina la lógica con la intuición. Es ideal para personas con ansiedad o pensamientos acelerados. Mejora la comunicación honesta y reduce el estrés.',
                'properties'       => [
                    'chakra'     => 'Garganta',
                    'beneficios' => ['Reduce la ansiedad', 'Calma pensamientos acelerados', 'Mejora la comunicación', 'Combina lógica e intuición', 'Autoconfianza', 'Sueño reparador'],
                    'combina_con'=> ['Amatista', 'Lapislázuli'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 9,
                'featured'         => false,
            ],
            [
                'name'             => 'Botella Cuarzo Blanco',
                'slug'             => 'botella-cuarzo-blanco',
                'short_description'=> 'El maestro sanador. Amplifica toda energía.',
                'description'      => 'El cuarzo blanco es el cristal maestro. Amplifica la energía de otras piedras, purifica el ambiente y armoniza todos los chakras. Es el punto de partida ideal para comenzar con los cristales.',
                'properties'       => [
                    'chakra'     => 'Todos los chakras',
                    'beneficios' => ['Amplifica energías', 'Purifica el ambiente', 'Armoniza todos los chakras', 'El maestro sanador', 'Potencia otras piedras', 'Claridad mental'],
                    'combina_con'=> ['Cualquier cristal'],
                ],
                'price_retail'     => 9500,
                'price_wholesale'  => 7000,
                'stock'            => 25,
                'featured'         => false,
            ],
        ];

        foreach ($cristales_botellas as $i => $data) {
            Product::create(array_merge($data, [
                'category_id' => $botellas->id,
                'sort_order'  => $i + 1,
            ]));
        }

        // Torres de Cristal
        $torres_data = [
            [
                'name'             => 'Torre Amatista',
                'slug'             => 'torre-amatista',
                'short_description'=> 'Punta natural de amatista.',
                'description'      => 'Torre o punta natural de amatista para decoración, meditación y trabajo energético. Irradia calma y protección en cualquier espacio.',
                'properties'       => ['chakra' => 'Corona', 'beneficios' => ['Calma', 'Protección', 'Meditación', 'Decoración']],
                'price_retail'     => 5500,
                'price_wholesale'  => 4000,
                'stock'            => 20,
                'featured'         => false,
            ],
            [
                'name'             => 'Torre Ojo de Tigre',
                'slug'             => 'torre-ojo-de-tigre',
                'short_description'=> 'Punta natural de ojo de tigre.',
                'description'      => 'Torre o punta natural de ojo de tigre. Su energía dorada atrae abundancia y éxito. Perfecta para el escritorio o espacio de trabajo.',
                'properties'       => ['chakra' => 'Plexo Solar', 'beneficios' => ['Abundancia', 'Éxito', 'Confianza', 'Decoración']],
                'price_retail'     => 5500,
                'price_wholesale'  => 4000,
                'stock'            => 15,
                'featured'         => false,
            ],
        ];

        foreach ($torres_data as $i => $data) {
            Product::create(array_merge($data, [
                'category_id' => $torres->id,
                'sort_order'  => $i + 1,
            ]));
        }

        // Oráculos
        $oraculos_data = [
            [
                'name'             => 'Oráculo Ángeles',
                'slug'             => 'oraculo-angeles',
                'short_description'=> 'Mensajes de tus ángeles guardianes.',
                'description'      => 'Baraja de oráculo con mensajes angélicos para guiar tu camino. Incluye 44 cartas ilustradas y manual en español.',
                'properties'       => ['tipo' => 'Oráculo', 'cartas' => 44, 'idioma' => 'Español'],
                'price_retail'     => 7500,
                'price_wholesale'  => 5500,
                'stock'            => 10,
                'featured'         => false,
            ],
        ];

        foreach ($oraculos_data as $i => $data) {
            Product::create(array_merge($data, [
                'category_id' => $oraculos->id,
                'sort_order'  => $i + 1,
            ]));
        }
    }
}
