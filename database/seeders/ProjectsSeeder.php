<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    /**
     * Reprend les données issues de la base vapaille_portfolio
     * (tables projetsweb, imagesweb, caracteristiques, fk_techno, techno).
     * Les projets graphiques (*gfx) ne sont volontairement pas repris ici.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'AlleyOOP',
                'category' => 'Développement Web',
                'date_label' => 'Janvier 2025',
                'url' => 'https://wembinventory.vapaille.projetsmmichamps.fr',
                'main_image' => 'img/WEB/alleyoop1.jpg',
                'mockup_image' => 'img/WEB/mockupalleyoop.jpg',
                'presentation' => "AlleyOOP est un projet web interactif destiné aux fans de NBA. Il permet d’explorer les équipes de basket américaines à travers des fiches stylisées et informatives, tout en rendant la navigation simple et ludique.<br><br>Le site repose sur une base de données PHP / phpMyAdmin et permet : de parcourir les équipes NBA, de rechercher et filtrer les équipes par conférence et par nombre de titres, d’ajouter des joueurs aux équipes, offrant une dimension participative appréciée des fans.<br><br>AlleyOOP est un site actif et visitable, pensé pour être accessible et intuitif. Il propose une exploration fluide de l’univers NBA, mêlant données, interactivité et design.<br><br>Compétences démontrées : <br>Développement back-end PHP, gestion de base de données, interfaces interactives, filtrage de données, UX orientée utilisateur",
                'role' => 'Développeur Front & Back',
                'duration' => '3 mois',
                'short_description' => 'AlleyOOP est un site interactif qui permet aux fans NBA de se promener à travers les équipes de basket américaines tout en apprenant des informations sur celles-ci.',
                'status' => 'Actif / Visitable',
                'context' => "Projet étudiant | réalisation d'un inventaire Web",
                'is_featured' => false,
                'images' => ['img/WEB/alleyoop1.jpg', 'img/WEB/alleyoop2.jpg', 'img/WEB/alleyoop3.jpg', 'img/WEB/alleyoop4.jpg', 'img/WEB/alleyoop5.jpg', 'img/WEB/alleyoop7.jpg', 'img/WEB/alleyoop8.jpg'],
                'technologies' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'MySQL', 'Photoshop', 'Figma'],
                'features' => [
                    ['title' => 'Découverte de PHP et PHPMyAdmin', 'description' => "C'est au cours de ce projet que j'ai découvert le langage PHP ainsi que la création de bases de données avec phpMyAdmin.", 'icon_class' => 'bx bxl-php'],
                    ['title' => 'SQL et liaison entre la bdd et le site', 'description' => "Grâce aux requêtes SQL j'ai également appris à lier une base de données à un site web, ce qui m'a permis de rendre le contenu dynamique et interactif.", 'icon_class' => 'bx bxl-postgresql'],
                    ['title' => 'Sous domaine', 'description' => "Pour répondre aux consignes de ce projet étudiant, j'ai dû apprendre à créer un sous domaine pour y mettre mon site.", 'icon_class' => 'bx bx-globe'],
                    ['title' => 'Responsive', 'description' => "J'ai tenu à ce que ce site soit responsive afin qu'il puisse également être utilisé sur téléphone.", 'icon_class' => 'bx bxs-devices'],
                ],
            ],
            [
                'title' => 'Bulle de bonheur | location de jardin/piscine',
                'category' => 'Développement web full stack',
                'date_label' => 'Avril 2025',
                'url' => 'https://bulledebonheur.org',
                'main_image' => 'img/WEB/bdb1.jpg',
                'mockup_image' => 'img/WEB/mockupbdb.jpg',
                'presentation' => "Ce projet est un projet professionnel, réalisé à la demande d'un particulier après la découverte de mes précédentes réalisations. L'objectif était de concevoir le site Bulle de Bonheur, un service de location de jardin et piscine, permettant aux clients de réserver facilement des créneaux définis à l'avance par la propriétaire.<br><br>Le site devait être simple d'utilisation pour les visiteurs tout en offrant à la propriétaire une autonomie totale dans la gestion des disponibilités. Le projet impliquait des contraintes réelles : attentes précises, délais de développement, fiabilité du système de réservation et visibilité du site sur les moteurs de recherche.<br><br>J'ai assuré la conception et le développement complets du site. Un back-office sécurisé par mot de passe a été mis en place afin de permettre à la propriétaire de créer, modifier et gérer les plages horaires disponibles. Le site a été conçu en responsive design, garantissant une utilisation fluide sur ordinateur, tablette et mobile. J'ai également pris en charge l'optimisation SEO et l'indexation manuelle du site sur Google afin d'assurer sa visibilité dans les résultats de recherche.<br><br>Le site est aujourd'hui actif et utilisable, répondant pleinement aux besoins du client.<br><br>Compétences démontrées : <br>Développement web front-end & back-end, conception de système de réservation, création de back-office sécurisé, gestion de projet client, SEO, responsive design, respect de contraintes professionnelles",
                'role' => 'Développeur, graphiste',
                'duration' => 'Base réalisée en 2 semaines intensives, et évolution constante sur le site',
                'short_description' => "L'objectif initial était de créer un site permettant à n'importe qui de réserver un créneau parmi ceux définis au préalable par la propriétaire.",
                'status' => 'Actif / Utilisable',
                'context' => 'Projet professionnel',
                'is_featured' => true,
                'images' => ['img/WEB/bdb1.jpg', 'img/WEB/bdb2.jpg', 'img/WEB/bdb3.jpg', 'img/WEB/bdb4.jpg', 'img/WEB/bdb5.jpg', 'img/WEB/bdb6.jpg'],
                'technologies' => ['PHP', 'MySQL', 'CSS', 'HTML', 'JavaScript', 'Tailwind', 'FileZilla'],
                'features' => [
                    ['title' => "Projet professionnel sur demande d'un particulier", 'description' => "L'une de mes premières expériences de création web avec des attentes précises et des contraintes de temps en matière de développement.", 'icon_class' => 'bx bx-clock-2'],
                    ['title' => "Création d'un backoffice protégé par mdp", 'description' => "Pour permettre au propriétaire d'ajouter des plages horaires et d'en gérer la disponibilité, j'ai créé un espace administrateur protégé par un mot de passe.", 'icon_class' => 'bx bx-lock'],
                    ['title' => 'Référencement SEO et hébergement', 'description' => "J'ai indexé manuellement le site sur Google afin qu'il apparaisse dans les résultats de recherche pertinents. De plus, j'ai optimisé le site pour le référencement SEO.", 'icon_class' => 'bx bx-search-alt'],
                    ['title' => 'Site responsive', 'description' => "Pour faciliter l'accessibilité du site et améliorer son référencement SEO, je l'ai rendu compatible avec les ordinateurs, les tablettes et les téléphones.", 'icon_class' => 'bx bx-mobile'],
                ],
            ],
            [
                'title' => 'VINYL TO VIRAL',
                'category' => 'Développement Web',
                'date_label' => 'Octobre 2025',
                'url' => 'https://lohanvapaille.github.io/datavyse_sae',
                'main_image' => 'img/WEB/datavyse1.jpg',
                'mockup_image' => 'img/WEB/mockupdatavyse.jpg',
                'presentation' => "Dans le cadre d'une situation d'apprentissage et d'évaluation en BUT Métiers du Multimédia et de l'Internet, j'ai réalisé en binôme un projet de visualisation de données. Passionné de musique, j'ai proposé d'orienter le sujet vers des données musicales, avec l'objectif de raconter une histoire à travers des chiffres.<br><br>Le défi consistait à transformer un volume important de données brutes en visualisations claires, compréhensibles et engageantes.<br><br>Nous avons conçu VINYL TO VIRAL, un site interactif retraçant l'histoire du Billboard, le classement musical le plus influent au monde, depuis sa création jusqu'à aujourd'hui. Les données sont mises en valeur à travers différents dispositifs : courbes illustrant l'évolution des tendances musicales, podiums mettant en avant les titres dominants à une période donnée, frises chronologiques permettant de naviguer dans le temps.<br><br>Compétences démontrées : <br>Visualisation de données, traitement et lecture de données, conception d'interfaces interactives, narration par la donnée (data storytelling), travail en équipe, réflexion UX",
                'role' => 'Développeur FullStack',
                'duration' => '1 mois et demi',
                'short_description' => "VINYL TO VIRAL raconte l'histoire du Billboard, le classement musical le plus populaire de la planète, et permet d'interagir avec des données musicales.",
                'status' => 'Terminé / Actif / Consultable',
                'context' => 'Projet étudiant',
                'is_featured' => true,
                'images' => ['img/WEB/datavyse1.jpg', 'img/WEB/datavyse2.jpg', 'img/WEB/datavyse3.jpg'],
                'technologies' => ['HTML', 'ChartJS', 'CSS', 'JavaScript', 'Photoshop', 'API', 'Github'],
                'features' => [
                    ['title' => 'Visualisation de données', 'description' => "L'objectif est de donner vie aux données du Billboard. Découverte des Canvas et de la création de SVG.", 'icon_class' => 'bx bx-bar-chart'],
                    ['title' => 'ChartJS', 'description' => "Découverte de ChartJS à travers un graphique qui retrace l'évolution des genres musicaux dans le temps.", 'icon_class' => 'bx bx-line-chart'],
                    ['title' => "Travail d'équipe", 'description' => "Ce projet est un projet étudiant. J'étais accompagné d'une personne ayant un profil créatif. J'ai donc codé les maquettes de site qu'il m'avait fournit.", 'icon_class' => 'bx bx-group'],
                ],
            ],
            [
                'title' => 'Mon Blog Musical',
                'category' => 'Développement Web',
                'date_label' => 'Août 2025',
                'url' => 'https://music.lohanvapaille.fr',
                'main_image' => 'img/WEB/music1.jpg',
                'mockup_image' => 'img/WEB/mockupmusic.jpg',
                'presentation' => "Ce projet est un site web personnel né d'une passion pour la musique. Il a été conçu comme un blog immersif retraçant la carrière et l'univers de mes artistes préférés, avec l'objectif de partager cette passion, sans consignes imposées ni cadre académique strict.<br><br>L'enjeu principal était de créer une expérience immersive et esthétique tout en assurant une bonne lisibilité sur tous les supports. Un problème est apparu sur mobile : l'utilisation de backgrounds fixes dégradait fortement le rendu sur téléphone. Il a donc fallu repenser entièrement la présentation responsive sans perdre l'identité visuelle du projet.<br><br>J'ai assuré la conception et le développement complet du site, avec un back-office sécurisé par mot de passe pour gérer facilement les artistes, les musiques et les contenus.<br><br>Compétences démontrées : <br>Développement front-end & back-end, conception responsive avancée, adaptation UX selon les supports, intégration multimédia, conception de back-office, autonomie technique",
                'role' => 'Développeur Full Stack',
                'duration' => 'Développement constant',
                'short_description' => 'Mon blog musical sur lequel chaque page retrace la carrière de mes artistes préférés.',
                'status' => 'Actif / Dev constant',
                'context' => 'Projet personnel',
                'is_featured' => true,
                'images' => ['img/WEB/music1.jpg', 'img/WEB/music2.jpg', 'img/WEB/music3.jpg', 'img/WEB/music4.jpg', 'img/WEB/music5.jpg'],
                'technologies' => ['PHP', 'CSS', 'JavaScript', 'MySQL', 'Photoshop', 'Figma'],
                'features' => [
                    ['title' => 'Back-Office', 'description' => "Pour ajouter un artiste ou un album à mon site, on passe par un backoffice protégé par un mot de passe.", 'icon_class' => 'bx bxs-lock-alt'],
                    ['title' => 'Responsive', 'description' => "Le site est responsive, l'expérience reste optimale sur ordinateur ou tablette.", 'icon_class' => 'bx bx-devices'],
                    ['title' => 'Blog Personnel', 'description' => "Ce blog répertorie la carrière de tous mes artistes préférés et me permet de partager ma passion pour la musique.", 'icon_class' => 'bx bx-heart'],
                ],
            ],
            [
                'title' => 'SweetPlace',
                'category' => 'Développement web',
                'date_label' => 'Janvier 2026',
                'url' => 'https://sweetplace.shop',
                'main_image' => 'img/WEB/sweetplace1.jpg',
                'mockup_image' => 'img/WEB/mockupsweetplace.jpg',
                'presentation' => "SweetPlace est une plateforme web collaborative permettant aux utilisateurs de créer, personnaliser et partager des compositions de donuts et de bagels, à la manière d'un réseau social thématique.<br><br>Le projet nécessitait une gestion complète des utilisateurs et de leurs créations, ainsi qu'une interface intuitive malgré une logique fonctionnelle complexe.<br><br>J'ai assuré l'intégralité du développement front-end et back-end, ainsi que la création d'un back-office permettant la gestion des contenus et des utilisateurs. Face aux limites des maquettes, j'ai retravaillé moi-même l'UX/UI afin d'obtenir une interface claire, fonctionnelle et cohérente avec le concept.<br><br>Après 3 mois de développement, la plateforme est pleinement fonctionnelle, stable et utilisable.<br><br>Compétences démontrées : <br>Développement full-stack, conception de back-office, gestion de projet, résolution de problèmes UX/UI, autonomie technique",
                'role' => 'Développeur Back/Front & Designer',
                'duration' => '3 mois',
                'short_description' => 'SweetPlace est une plateforme collaborative et participative autour des compositions de donuts.',
                'status' => 'Actif / Utilisable',
                'context' => 'Projet universitaire en groupe',
                'is_featured' => false,
                'images' => ['img/WEB/sweetplace1.jpg', 'img/WEB/sweetplace2.jpg', 'img/WEB/sweetplace3.jpg', 'img/WEB/sweetplace4.jpg', 'img/WEB/sweetplace5.jpg', 'img/WEB/sweetplace6.jpg', 'img/WEB/sweetplace7.jpg', 'img/WEB/sweetplace8.jpg'],
                'technologies' => ['API', 'PHP', 'JavaScript', 'MySQL', 'Figma', 'HTML', 'CSS', 'Github', 'FileZilla'],
                'features' => [
                    ['title' => 'Mon plus gros projet', 'description' => "Ce projet contient un back-office, un système de création de compositions et d'images de donuts, un système de compte et de suivi (comme un réseau social), un système de like et de panier.", 'icon_class' => 'bx bxs-balloon'],
                    ['title' => 'Responsive', 'description' => "J'ai tenu à ce que ce site soit responsive afin qu'il puisse également être utilisé sur téléphone.", 'icon_class' => 'bx bxs-devices'],
                    ['title' => 'Réseau social', 'description' => "Cette plateforme est similaire à un réseau social. On peut créer un compte, poster des compositions, suivre des utilisateurs, liker leurs compositions ou les mettre dans son panier.", 'icon_class' => 'bx bx-link'],
                    ['title' => 'Travail de groupe', 'description' => "Dans ce projet, j'ai été chef de projet ainsi que développeur back-end et front-end, accompagné d'illustrateurs et de maquettistes pour le design.", 'icon_class' => 'bx bxs-group'],
                ],
            ],
            [
                'title' => 'Site de la TIM (semestre au Canada)',
                'category' => 'Développement web',
                'date_label' => 'Mars 2026',
                'url' => 'https://tim-canada.lohanvapaille.fr',
                'main_image' => 'img/WEB/tim1.jpg',
                'mockup_image' => 'img/WEB/mockuptim.jpg',
                'presentation' => "Ce projet a été conçu pour mettre en avant la formation Techniques d'intégration multimédia au Canada. Il permet de découvrir l'univers du programme à travers une interface moderne et structurée, offrant une vitrine claire et dynamique aux futurs étudiants et partenaires.<br><br>Le site, développé lors d'un échange universitaire, repose sur une architecture optimisée et permet de présenter les axes majeurs de la formation, de naviguer intuitivement à travers les contenus pédagogiques, et d'offrir une expérience utilisateur fluide.<br><br>Compétences démontrées : <br>Développement front-end, intégration multimédia, architecture de l'information, design d'interface (UI), réflexion UX et gestion de projet numérique.",
                'role' => 'Développeur, Concepteur',
                'duration' => '2 mois',
                'short_description' => "Ce projet web a été conçu pour mettre en avant la formation Techniques d'intégration multimédia au Canada.",
                'status' => 'Actif / Utilisable',
                'context' => "Semestre d'étude au Canada",
                'is_featured' => false,
                'images' => ['img/WEB/tim1.jpg'],
                'technologies' => ['MVC', 'Node.js', 'Composer', 'Figma', 'BladeOne', 'Photoshop', 'HTML', 'MySQL'],
                'features' => [
                    ['title' => 'Architecture MVC', 'description' => "La consigne imposait le développement du site avec une architecture Modèle-Vue-Contrôleur. Je me suis donc adapté et j'ai fortement apprécié cette logique.", 'icon_class' => 'bx bxl-php'],
                    ['title' => 'Semestre au Canada', 'description' => "Ce projet a été conçu pendant mon semestre d'études au Canada, à Québec, dans le cadre du programme TIM.", 'icon_class' => 'bx bxs-briefcase-alt-2'],
                    ['title' => 'Conception et développement autonome', 'description' => "J'ai conçu le site seul de A à Z, à partir d'une base de données commune. Chaque élève exprimait son univers personnel pour créer le site du programme.", 'icon_class' => 'bx bxs-user'],
                ],
            ],
            [
                'title' => 'HuntersTeam',
                'category' => 'Développement web',
                'date_label' => 'Avril 2026',
                'url' => 'https://huntersteam.fr',
                'main_image' => 'img/WEB/huntersteam1.jpg',
                'mockup_image' => 'img/WEB/mockuphuntersteam.jpg',
                'presentation' => "Ce projet a été conçu pour mettre en avant l'association sportive Hunters Team à travers une plateforme web sur mesure. Il permet de découvrir l'univers du club (MMA, Boxe, Grappling) à travers une interface moderne et structurée.<br><br>Le site, développé dans le cadre d'un stage de fin d'études, repose sur une architecture MVC optimisée et sécurisée. Il permet de présenter les disciplines et le planning des cours, de gérer les réservations de cours d'essai gratuits, de réserver des produits dérivés, et de faciliter l'inscription des membres en ligne via une intégration avec HelloAsso.<br><br>Compétences démontrées : <br>Développement full-stack (PHP, architecture MVC, SQL), intégration d'API tierces (HelloAsso), sécurisation des accès et conformité légale, réflexion UX/UI, gestion de projet numérique en autonomie.",
                'role' => 'Développeur back-end et full-stack',
                'duration' => '2 mois et demi',
                'short_description' => "Ce projet a été conçu pour mettre en avant l'association sportive Hunters Team à travers une plateforme web sur mesure.",
                'status' => 'Actif / Utilisable',
                'context' => 'Association concrète et déclarée',
                'is_featured' => true,
                'images' => ['img/WEB/huntersteam1.jpg', 'img/WEB/huntersteam2.jpg', 'img/WEB/huntersteam3.jpg', 'img/WEB/huntersteam4.jpg', 'img/WEB/huntersteam5.webp', 'img/WEB/huntersteam6.webp'],
                'technologies' => ['MVC', 'Laravel', 'PHP', 'MySQL', 'BladeOne', 'Vite.js'],
                'features' => [
                    ['title' => 'Découverte de Laravel', 'description' => "Dans ce stage, j'ai pu découvrir et coder avec le framework PHP Laravel.", 'icon_class' => 'bx bxl-php'],
                    ['title' => 'Stage de deuxième année de MMI', 'description' => "Ce site a été conçu dans le cadre de mon stage de deuxième année de BUT MMI.", 'icon_class' => 'bx bxs-briefcase-alt-2'],
                    ['title' => "Travail d'équipe à l'échelle d'une entreprise", 'description' => "Pour la première fois, j'ai intégré une équipe de conception web comprenant 2 développeurs, un graphiste, deux concepteurs Figma, et une chef de projet.", 'icon_class' => 'bx bx-group'],
                ],
            ],
            [
                'title' => 'Du Droit aux Étoiles (Articles juridiques)',
                'category' => 'Développement Web',
                'date_label' => 'Juin 2026',
                'url' => 'https://melaniejamain.fr',
                'main_image' => 'img/WEB/mel1.jpg',
                'mockup_image' => null,
                'presentation' => "Ce projet a été conçu pour mettre en avant l'expertise juridique d'une rédactrice passionnée à travers une thématique unique : le croisement entre le droit spatial et la place de la femme.<br><br>Il permet de découvrir l'univers de la recherche et de l'actualité juridique à travers une interface moderne et structurée, offrant une vitrine claire et dynamique aux professionnels, étudiants et passionnés du secteur.<br><br>Le site, développé sur mesure, repose sur une architecture MVC optimisée et permet de présenter des articles de fond, de naviguer intuitivement à travers les catégories thématiques, et d'offrir un back-office équipé de l'éditeur Quill.js pour une gestion autonome du contenu.<br><br>Compétences démontrées : <br>Développement full-stack (architecture MVC), intégration d'outils tiers (Quill.js), gestion de base de données (CRUD), design d'interface (UI) et conception de back-office sécurisé.",
                'role' => 'Développement Full Stack',
                'duration' => '1 semaine',
                'short_description' => "Ce projet a été conçu pour mettre en avant l'expertise juridique d'une rédactrice passionnée, à la croisée du droit spatial et de la place de la femme.",
                'status' => 'Actif / Visitable',
                'context' => 'Projet pour un client',
                'is_featured' => true,
                'images' => ['img/WEB/mel1.jpg', 'img/WEB/mel2.jpg', 'img/WEB/mel3.jpg', 'img/WEB/mel4.jpg'],
                'technologies' => [],
                'features' => [
                    ['title' => 'Quill.js', 'description' => "Le confort d'écriture est renforcé par l'intégration de Quill.js, un éditeur de texte interactif permettant à la rédactrice de mettre en forme et de publier ses articles directement depuis le back-office.", 'icon_class' => 'bx bx-edit-alt'],
                ],
            ],
        ];

        foreach ($projects as $data) {
            $images = $data['images'];
            $technologies = $data['technologies'];
            $features = $data['features'];
            unset($data['images'], $data['technologies'], $data['features']);

            $project = Project::create($data);

            foreach ($images as $path) {
                $project->images()->create(['path' => $path]);
            }

            foreach ($features as $feature) {
                $project->features()->create($feature);
            }

            if (!empty($technologies)) {
                $technoIds = collect($technologies)->map(
                    fn($name) => Technology::firstOrCreate(['name' => $name])->id
                );
                $project->technologies()->sync($technoIds);
            }
        }
    }
}