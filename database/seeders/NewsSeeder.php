<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Actualités initiales, rédigées à partir de sources publiques (citées dans chaque article)
 * et des données officielles FFVolley importées sur le site.
 */
class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', 'super_admin')->value('id');
        $cat = Category::pluck('id', 'slug');

        foreach ($this->articles() as $article) {
            News::updateOrCreate(['slug' => $article['slug']], [
                'title' => $article['title'],
                'excerpt' => $article['excerpt'],
                'content' => safe_html($article['content']),
                'image' => $article['image'] ?? null,
                'image_credit' => $article['credit'] ?? null,
                'source_url' => $article['source'] ?? null,
                'category_id' => $cat[$article['category']] ?? $cat->first(),
                'user_id' => $author,
                'status' => 'published',
                'featured' => $article['featured'] ?? false,
                'published_at' => Carbon::parse($article['date'], config('app.timezone')),
            ]);
        }
    }

    private function video(string $id, string $title): string
    {
        return '<p><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/'.$id.'" title="'.e($title).'" frameborder="0" allowfullscreen></iframe></p>';
    }

    private function articles(): array
    {
        return [
            [
                'slug' => 'saison-2026-2027-c-est-reparti',
                'date' => '2026-10-05 09:00',
                'featured' => true,
                'category' => 'match',
                'image' => 'images/photos/indoor-smash-1.jpg',
                'credit' => 'Sir Jack Bumotad / Unsplash (illustration)',
                'source' => 'https://www.ffvbbeach.org/ffvbapp/resu/vbspo_home.php?saison=2026/2027&codent=LIMART',
                'title' => 'Saison 2026-2027 : les championnats sont lancés',
                'excerpt' => 'Seniors et M18/M21 ont repris le chemin des gymnases fin septembre. Premiers résultats, nouveautés et format : tout ce qu\'il faut savoir sur la nouvelle saison.',
                'content' => <<<'HTML'
<p>La saison 2026-2027 de la Ligue Martiniquaise de Volley-Ball est officiellement lancée. Quatre poules sont publiées sur la plateforme officielle de la FFVolley : le <strong>championnat senior masculin</strong> et le <strong>championnat senior féminin</strong>, chacun en poule unique, ainsi que les championnats <strong>M18/M21 masculin et féminin</strong>.</p>
<h2>Les premiers résultats chez les seniors masculins</h2>
<ul>
<li>Racing Club Arlésien 3-0 Good-Luck Volley-Ball 2 (25-16, 25-19, 25-12), le 26 septembre aux Anses-d'Arlet ;</li>
<li>Good-Luck Volley-Ball 2-3 Espoir de Sainte-Luce (25-18, 25-27, 25-19, 20-25, 10-15), le 26 septembre ;</li>
<li>Espoir de Sainte-Luce 1-3 Racing Club de Rivière-Pilote (22-25, 18-25, 25-22, 23-25), le 3 octobre à Sainte-Luce.</li>
</ul>
<p>À noter, l'engagement du <strong>Pôle Performance</strong> en championnat senior masculin, aux côtés des clubs de l'île : une belle occasion pour les jeunes talents de se frotter au plus haut niveau régional.</p>
<h2>Résultats en direct sur le site</h2>
<p>Bonne nouvelle pour les supporters : les calendriers, scores (avec le détail des sets) et classements sont désormais <strong>mis à jour automatiquement</strong> sur ce site à partir des données officielles de la FFVolley. Rendez-vous dans la rubrique <a href="/competitions">Compétitions</a> pour suivre votre équipe.</p>
HTML,
            ],
            [
                'slug' => 'muc-et-rayon-champions-de-martinique-2026',
                'date' => '2026-04-19 10:00',
                'category' => 'match',
                'image' => 'images/photos/indoor-celebration.jpg',
                'credit' => 'Vince Fleming / Unsplash (illustration)',
                'source' => 'https://rci.fm/martinique/infos/Sport/Volley-Ball-le-Rayon-chez-les-filles-et-le-MUC-chez-les-garcons-sont-champions-de',
                'title' => 'Le Rayon et le MUC sacrés champions de Martinique',
                'excerpt' => 'Triplé pour les filles du Rayon de Petite Anse, premier titre pour le MUC au terme d\'une finale renversante face au Racing Club Arlésien.',
                'content' => <<<'HTML'
<p>Les finales des play-offs 2026 ont livré leur verdict. Chez les femmes, <strong>le Rayon de Petite Anse</strong> s'est imposé 3-0 face à l'Empire Volley-Ball Club de La Trinité et décroche ainsi un troisième titre consécutif. Loanne Bengaber a été élue meilleure joueuse de la finale.</p>
<p>Chez les hommes, la finale a tenu toutes ses promesses. Mené deux sets à zéro par le <strong>Racing Club Arlésien</strong> (18-25, 16-25), le <strong>Martinique Université Club</strong> a renversé la rencontre (25-23, 26-24) avant de dominer le tie-break 15-6. Un premier titre pour le MUC depuis sa reformation il y a trois ans ; son capitaine Lucas Vergne a été désigné MVP.</p>
<p>En saison régulière, le MUC avait déjà terminé en tête du championnat senior masculin avec 15 victoires en 16 matchs, à égalité de points (42) avec le RCA.</p>
<h2>Revivez la finale masculine</h2>
HTML
                    .$this->video('UQkr4CXVuMQ', 'Finale des play-offs 2026 : MUC – RCA'),
            ],
            [
                'slug' => 'beach-volley-martinique-jeux-amerique-centrale-caraibes-2026',
                'date' => '2026-07-26 09:00',
                'category' => 'beach',
                'image' => 'images/photos/beach-2.jpg',
                'credit' => 'Paulo Henrique Macedo Dias / Unsplash (illustration)',
                'source' => 'https://rci.fm/martinique/infos/Sport/La-Martinique-sera-representee-aux-Jeux-dAmerique-centrale-et-de-la-Caraibe-2026',
                'title' => 'Beach-volley : deux paires martiniquaises aux Jeux d\'Amérique centrale et des Caraïbes',
                'excerpt' => 'Noah et Elowan Choux, Karine François et Carole Hervé portent les couleurs de la Martinique sur le sable de Saint-Domingue.',
                'content' => <<<'HTML'
<p>La Martinique est représentée aux <strong>Jeux d'Amérique centrale et des Caraïbes 2026</strong>, organisés à Saint-Domingue (République dominicaine) du 24 juillet au 8 août, avec une délégation de 18 athlètes. Le beach-volley y tient une belle place avec deux paires engagées :</p>
<ul>
<li>chez les hommes, <strong>Noah et Elowan Choux</strong> ;</li>
<li>chez les femmes, <strong>Karine François et Carole Hervé</strong>.</li>
</ul>
<p>Pour leur entrée dans le tournoi le 25 juillet, les frères Choux se sont inclinés face au Nicaragua (21-8, 21-9). Une expérience précieuse au plus haut niveau régional pour ces jeunes joueurs, déjà finalistes du championnat de beach jeunes en Martinique.</p>
<p>Sources : RCI, <a href="https://norceca.info/mens-beach-volleyball-gets-underway-at-santo-domingo-2026/">NORCECA</a>.</p>
HTML,
            ],
            [
                'slug' => 'coupe-antilles-2026-riviere-salee',
                'date' => '2026-06-07 10:00',
                'category' => 'match',
                'image' => 'images/photos/indoor-bloc.jpg',
                'credit' => 'Horserat / Unsplash (illustration)',
                'source' => 'https://rci.fm/deuxiles/infos/Sport/Volley-Ball-Coupe-Antilles-la-Guadeloupe-fait-carton-plein-chez-les-femmes-et-chez-les',
                'title' => 'Coupe Antilles : la Guadeloupe s\'impose à Rivière-Salée',
                'excerpt' => 'Malgré un premier match remporté par le Rayon, les clubs guadeloupéens repartent avec les deux trophées de la Coupe Antilles 2026.',
                'content' => <<<'HTML'
<p>Les 5 et 6 juin 2026, le Palais des sports de Rivière-Salée accueillait la <strong>Coupe Antilles</strong>, qui oppose les champions de Martinique et de Guadeloupe en matchs aller-retour.</p>
<p>Chez les femmes, <strong>le Rayon de Petite Anse</strong> a remporté le match aller au tie-break face à l'Arsenal de Petit-Bourg. Au retour, les Guadeloupéennes ont gagné 3-0 puis arraché le set en or 15-11 pour s'adjuger le titre.</p>
<p>Chez les hommes, le <strong>MUC</strong>, champion et vainqueur de la Coupe de Martinique, s'est incliné deux fois 3-1 face à l'ASC Fumerolles.</p>
<p>Les résultats détaillés sont disponibles dans la rubrique <a href="/competitions">Compétitions</a> (Tournoi des champions et des championnes Antilles).</p>
HTML,
            ],
            [
                'slug' => 'beach-volley-finales-championnat-2026-en-video',
                'date' => '2026-06-20 09:00',
                'category' => 'beach',
                'image' => 'images/photos/beach-6-caraibes.jpg',
                'credit' => 'Ginabell Andujar / Unsplash (illustration)',
                'source' => 'https://www.youtube.com/@lmvb972liguemartiniquaised6',
                'title' => 'Championnat de beach-volley 2026 : revivez les finales en vidéo',
                'excerpt' => 'Finales jeunes et féminines du championnat de Martinique de beach-volley : les matchs sont disponibles en intégralité sur la chaîne YouTube de la ligue.',
                'content' => '<p>Le championnat de Martinique de beach-volley 2026 a connu ses finales en juin. Retrouvez les rencontres filmées par la ligue.</p><h2>Finale jeunes (13 juin 2026)</h2>'
                    .$this->video('Q2MkWo96vns', 'Finale beach-volley jeunes 2026')
                    .'<h2>Finale féminine (juin 2026)</h2>'
                    .$this->video('9_IJs2pIUKE', 'Finale beach-volley femmes 2026')
                    .'<p>Toutes les vidéos de la ligue sont sur la <a href="https://www.youtube.com/@lmvb972liguemartiniquaised6">chaîne YouTube LMVB972</a>.</p>',
            ],
            [
                'slug' => 'cazova-u23-les-martiniquaises-championnes-de-la-caraibe',
                'date' => '2025-07-16 09:00',
                'category' => 'equipe',
                'image' => 'images/photos/divers-medailles.jpg',
                'credit' => 'George Pisarevsky / Unsplash (illustration)',
                'source' => 'https://norceca.info/?p=21470',
                'title' => 'CAZOVA U23 : les Martiniquaises championnes de la Caraïbe, le bronze pour les garçons',
                'excerpt' => 'À Trinité-et-Tobago, la sélection féminine U23 a battu le Suriname en finale et décroché le titre caribéen. Les garçons montent sur la troisième marche.',
                'content' => <<<'HTML'
<p>Exploit pour le volley martiniquais ! Le 13 juillet 2025 à Maloney (Trinité-et-Tobago), la <strong>sélection féminine U23 de Martinique</strong> a remporté le championnat CAZOVA en battant le Suriname, tenant du titre, 3 sets à 1 (23-25, 25-15, 25-21, 25-19).</p>
<p>Les joueuses d'Eddy Erialc ont réalisé un parcours parfait, sans la moindre défaite, en dominant notamment le Suriname, Trinité-et-Tobago et Curaçao en phase de poule. La capitaine <strong>Maelyss Melinard-Chanteur</strong> a été élue meilleure joueuse et meilleure attaquante du tournoi. Ce titre ouvre aux Martiniquaises les portes de la Coupe panaméricaine junior.</p>
<h2>Le bronze pour les garçons</h2>
<p>Chez les U23 masculins, après deux défaites face à Trinité-et-Tobago et au Suriname, la Martinique a su réagir pour s'offrir la médaille de bronze en battant la Guadeloupe 3-1 (18-25, 25-22, 25-19, 25-21).</p>
<p>Sources : <a href="https://norceca.info/?p=21470">NORCECA</a>, <a href="https://norceca.info/?p=21464">NORCECA</a>, RCI.</p>
HTML,
            ],
            [
                'slug' => 'maeva-labylle-reelue-presidente-de-la-ligue',
                'date' => '2024-12-03 09:00',
                'category' => 'evenement',
                'image' => 'images/board/maeva-antiste-large.jpg',
                'credit' => 'DR / Martinique la 1ère',
                'source' => 'https://la1ere.franceinfo.fr/martinique/maeva-labylle-reelue-presidente-de-la-ligue-de-volley-ball-de-martinique-1542253.html',
                'title' => 'Maëva Labylle réélue présidente de la ligue pour quatre ans',
                'excerpt' => 'Lors de l\'assemblée générale du 2 décembre 2024, Maëva Labylle (Maëva Antiste) a été réélue à la tête de la Ligue de volley-ball de Martinique pour un mandat de quatre ans.',
                'content' => <<<'HTML'
<p>Réunie en assemblée générale le 2 décembre 2024, la Ligue de volley-ball de Martinique a reconduit <strong>Maëva Labylle</strong> (aujourd'hui Maëva Antiste) à sa présidence pour un mandat de quatre ans.</p>
<p>Cette réélection s'inscrit dans la continuité du travail engagé depuis 2024 par la nouvelle équipe dirigeante pour redresser la ligue, après la révocation de l'ancien comité directeur en octobre 2023 et la mise en place d'un plan d'assainissement des finances avec l'appui de la Fédération Française de Volley.</p>
<p>Objectifs affichés : stabiliser la gestion, relancer durablement les championnats seniors et jeunes, développer le beach-volley et accompagner les sélections dans les compétitions caribéennes.</p>
<p>Sources : <a href="https://la1ere.franceinfo.fr/martinique/maeva-labylle-reelue-presidente-de-la-ligue-de-volley-ball-de-martinique-1542253.html">Martinique la 1ère</a>, <a href="https://rci.fm/martinique/infos/Sport/Apres-de-graves-manquements-une-nouvelle-equipe-la-tete-de-la-Ligue-de-Volleyball-de">RCI</a>.</p>
HTML,
            ],
        ];
    }
}
