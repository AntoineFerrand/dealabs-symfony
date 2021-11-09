**Readme du Dealabs (Farjas Alexandre et Ferrand Antoine)**

**Installer l'environnement**

- composer install : Pour installer les dépendances dont notre projet Symfony a besoin ;
- npm install : Pour installer toutes les bibliothèques que nous utilisons dans notre projet ;
- yarn install : Pour utiliser webpack
- yarn envore dev : Pour que webpack prennent nos assets, bootstrap et autres pour les « minimifier »

**La base données et les mails**

créer un fichier «.env.local » en copiant collant le .env et en le renommant .env.local.
Ensuite paramétrez l'url de votre BDD (DATABASE_URL) ;
Enfin paramétrez votre MAILER_DSN pour gérer la receptions des mails.
Pour effectuer nos tests nous avons utilisé Mailtrap.io, un site sur lequel on peut tester l'envoie de faux mails très simplement.
Nous avions pour configuration: MAILER_DSN=smtp://062faeb870868a:fdda6b3da9ee04@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login

Une fois ceci effectué, créons de la BDD et remplissons la avec nos fixtures:
- php bin/console doctrine:database:create Pour créer la base si elle n'existe pas encore ;
- php bin/console make:migration Pour créer un fichier de migration contenant toutes les modifications à exécuter pour créer des données en bases cohérentes par rapport aux entités.
- php bin/console doctrine:migrations:migrate pour effectuer la migration (créer les différentes tables)
- php bin/console doctrine:fixtures:load pour générer les données démo via Faker

**Alertes**

Pour envoyer des notifications aux utilisateurs pour les alertes pour lesquelles ils veulent être notifier par mail,
il faut lancer la commande:
- php bin/console send-notif
