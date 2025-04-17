<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            ['idx' => 1, 'rank' => "Cel Av", 'name' => "Diogo Silva CASTILHO", 'login' => "castilhodsc@fab.mil.br", 'order_number' => "3047512"],
            ['idx' => 2, 'rank' => "Ten Cel Eng", 'name' => "PAULO CÉSAR da Silva Guimarães", 'login' => "paulocesarpcsg@fab.mil.br", 'order_number' => "3432831"],
            ['idx' => 3, 'rank' => "Ten Cel Eng", 'name' => "Francisco de MATTOS BRITO Junior", 'login' => "mattosbritofmbj@fab.mil.br", 'order_number' => "3686515"],
            ['idx' => 4, 'rank' => "Maj QOAV NTE", 'name' => "Thiago Romeiro CAPUCHINHO", 'login' => "capuchinhotrc@fab.mil.br", 'order_number' => "3490351"],
            ['idx' => 5, 'rank' => "Cap Int", 'name' => "Renan de LACERDA Lima Gonçalves", 'login' => "lacerdarllg@fab.mil.br", 'order_number' => "4111281"],
            ['idx' => 6, 'rank' => "Cap Int", 'name' => "CHEYLA Cristina Silva Salvador", 'login' => "cheylaccss@fab.mil.br", 'order_number' => "6156550"],
            ['idx' => 7, 'rank' => "Cap Eng  ELN", 'name' => "Rafael MACÊDO Trindade", 'login' => "macedormt@fab.mil.br", 'order_number' => "6123120"],
            ['idx' => 8, 'rank' => "1° Ten QOCon ADM", 'name' => "CATIANA FARIA dos Santos", 'login' => "catianacfs@fab.mil.br", 'order_number' => "7391110"],
            ['idx' => 9, 'rank' => "1° Ten QOCon ADM", 'name' => "ANGELA de Lima MILITÃO", 'login' => "angelamilitaoalm@fab.mil.br", 'order_number' => "7391188"],
            ['idx' => 10, 'rank' => "2° Ten QOCon CCO", 'name' => "TATIANA Sousa da ROCHA", 'login' => "tatianarochatsr@fab.mil.br", 'order_number' => "7433794"],
            ['idx' => 11, 'rank' => "2° Ten QOCon CCO", 'name' => "MARIANA RODRIGUES Queiroz Moreira", 'login' => "marianarodriguesmrqm@fab.mil.br", 'order_number' => "7432445"],
            ['idx' => 12, 'rank' => "2° Ten QOCon PRU", 'name' => "Matheus PRADO", 'login' => "pradomp@fab.mil.br", 'order_number' => "7534710"],
            ['idx' => 13, 'rank' => "2° Ten QOCon CCO", 'name' => "ANA Cláudia Aparecida PRIANTE", 'login' => "anaprianteacap@fab.mil.br", 'order_number' => "7537301"],
            ['idx' => 14, 'rank' => "2° Ten QOCon ADM", 'name' => "CARLA Pereira Machado Homem", 'login' => "carlacpmh@fab.mil.br", 'order_number' => "7623070"],
            ['idx' => 15, 'rank' => "SO BMA", 'name' => "Flávio de Souza MARTINO", 'login' => "martinofsm@fab.mil.br", 'order_number' => "2086735"],
            ['idx' => 16, 'rank' => "SO BMA", 'name' => "Marcos Antonio Muniz LOBO", 'login' => "lobomaml@fab.mil.br", 'order_number' => "2345560"],
            ['idx' => 17, 'rank' => "SO BMA", 'name' => "Roberval Corrêa ESPADIM", 'login' => "espadimrce@fab.mil.br", 'order_number' => "2477351"],
            ['idx' => 18, 'rank' => "SO SAD", 'name' => "Gilson CLEI José Barreto", 'login' => "cleigcjb@fab.mil.br", 'order_number' => "2961849"],
            ['idx' => 19, 'rank' => "SO BMA", 'name' => "MICHEL da Silva Soares", 'login' => "michelmss@fab.mil.br", 'order_number' => "3288536"],
            ['idx' => 20, 'rank' => "SO BET", 'name' => "Filipe ESTRELA Nunes", 'login' => "estrelafen@fab.mil.br", 'order_number' => "3381218"],
            ['idx' => 21, 'rank' => "SO SAD", 'name' => "Eduardo dos Santos ROCHA", 'login' => "rochaesr@fab.mil.br", 'order_number' => "4069390"],
            ['idx' => 22, 'rank' => "SO BET", 'name' => "DARIELE Elisa Reis Breginski", 'login' => "darielederb@fab.mil.br", 'order_number' => "4069323"],
            ['idx' => 23, 'rank' => "1S SAD", 'name' => "Anderson RUBIM Musi Dias", 'login' => "rubimarmd@fab.mil.br", 'order_number' => "3210685"],
            ['idx' => 24, 'rank' => "1S SAD", 'name' => "Raquel QUINTELA Gomes do Nascimento", 'login' => "quintelarqgn@fab.mil.br", 'order_number' => "4039769"],
            ['idx' => 25, 'rank' => "1S SAD", 'name' => "André da Silva BEMFICA", 'login' => "bemficaasb@fab.mil.br", 'order_number' => "3034968"],
            ['idx' => 26, 'rank' => "1S BMA", 'name' => "MOISES Ferreira da Silva", 'login' => "moisesmfs@fab.mil.br", 'order_number' => "2709988"],
            ['idx' => 27, 'rank' => "1S BMA", 'name' => "Francisco Lucivany FONTENELE Dias", 'login' => "fonteneleflfd@fab.mil.br", 'order_number' => "3424146"],
            ['idx' => 28, 'rank' => "1S BSP", 'name' => "Vagner de Oliveira BRASIL", 'login' => "brasilvob@fab.mil.br", 'order_number' => "3463907"],
            ['idx' => 29, 'rank' => "1S BSP", 'name' => "GISELE SILVA Odilon", 'login' => "giselesilvagso@fab.mil.br", 'order_number' => "4360389"],
            ['idx' => 30, 'rank' => "1S SAD", 'name' => "Marcelo LIMA da Silva", 'login' => "limamls@fab.mil.br", 'order_number' => "3467317"],
            ['idx' => 31, 'rank' => "2S BMB", 'name' => "FERNANDO dos Santos Souza", 'login' => "fernandofss@fab.mil.br", 'order_number' => "4112695"],
            ['idx' => 32, 'rank' => "2S BMA", 'name' => "Eric Tiago Zuchi de Andrade BRUM", 'login' => "brumetzab@fab.mil.br", 'order_number' => "6323847"],
            ['idx' => 33, 'rank' => "2S SAD", 'name' => "Maicon Fonseca AMADOR", 'login' => "amadormfa@fab.mil.br", 'order_number' => "4157940"],
            ['idx' => 34, 'rank' => "2S SAD", 'name' => "ROSEMERY de Cavalho Santos", 'login' => "rosemeryrcs@fab.mil.br", 'order_number' => "6450652"],
            ['idx' => 35, 'rank' => "3S TAD", 'name' => "Pedro Henrique dos Santos AMARO", 'login' => "amarophsa@fab.mil.br", 'order_number' => "6853501"],
            ['idx' => 36, 'rank' => "3S TIN", 'name' => "Paulo de Tarso Freitas BARBOSA", 'login' => "barbosaptfb@fab.mil.br", 'order_number' => "7419384"],
            ['idx' => 37, 'rank' => "CB SAD", 'name' => "João ANTONIO Teixeira", 'login' => "tp.antoniojat@fab.mil.br", 'order_number' => "7228678"],
            ['idx' => 38, 'rank' => "S1 SGS", 'name' => "Lucas Marques CAETANO", 'login' => "tp.caetanolmc@fab.mil.br", 'order_number' => "7264852"],
            ['idx' => 39, 'rank' => "S1 SAD", 'name' => "Gabriel Silva do NASCIMENTO", 'login' => "tp.nascimentogsn1@fab.mil.br", 'order_number' => "7326777"],
            ['idx' => 40, 'rank' => "S1 SAD", 'name' => "Caio Henrique SILVÉRIO da SILVA", 'login' => "tp.silveriosilvachss@fab.mil.br", 'order_number' => "7404409"],
            ['idx' => 41, 'rank' => "S1 SAD", 'name' => "Rodrigo VIEIRA de Oliveira", 'login' => "tp.rvieirarvo@fab.mil.br", 'order_number' => "7403526"],
            ['idx' => 42, 'rank' => "S2 SNE", 'name' => "Pedro HENRIQUE de Paula", 'login' => "phenriquephp@fab.mil.br", 'order_number' => "7402759"],
            ['idx' => 43, 'rank' => "S2 SNE", 'name' => "CASSIANO dos Santos Queiroz de Souza", 'login' => "tp.cassianocsqs@fab.mil.br", 'order_number' => "7515910"],
            ['idx' => 44, 'rank' => "S2 SNE", 'name' => "CAUÊ Irmão Firmino da Silva", 'login' => "tp.cauecifs@fab.mil.br", 'order_number' => "7605161"]
        ];

        foreach ($usuarios as $userData) {
            // Extrai a(s) parte(s) em maiúsculas do nome completo para usar como nome de guerra
            $nameParts = explode(' ', $userData['name']);
            $warNameParts = [];
            foreach ($nameParts as $part) {
                // Verifica se a parte está toda em maiúsculas (considerando acentos comuns)
                if (mb_strtoupper($part, 'UTF-8') === $part && ctype_upper(preg_replace('/[ÁÉÍÓÚÀÂÊÔÃÕÇ]/u', 'A', $part))) {
                    $warNameParts[] = $part;
                }
            }
            $warName = implode(' ', $warNameParts);
            // Se não encontrar parte em maiúsculas, usa a última palavra como fallback (comportamento anterior)
            if (empty($warName)) {
                 $warName = Str::upper(end($nameParts));
            }

            User::updateOrCreate(
                ['email' => $userData['login']], // Usar email como chave única
                [
                    'name' => $warName, // Usando o nome de guerra extraído
                    'rank' => $userData['rank'],
                    'full_name' => $userData['name'],
                    'order_number' => $userData['order_number'], // Usando o 'order_number' do array
                    'email' => $userData['login'],
                    'password' => Hash::make((string)$userData['order_number']), // Hashing do 'order_number' como senha
                ]
            );
        }
    }
}