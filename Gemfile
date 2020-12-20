source "https://rubygems.org"

# Capistrano & multiconfig/composer
gem "capistrano", "~> 3.11.2"
gem "capistrano-composer"
gem "capistrano-multiconfig"

# SSH & Git
gem 'capistrano-ssh-doctor', '~> 1.0'
gem 'capistrano-git-with-submodules', '~> 2.0'

# Notifications on MacOS
gem 'capistrano-nc', '~> 0.2'

# Symfony
gem 'capistrano-symfony'

########
# INFO #
########
#Graf : https://www.grafikart.fr/forum/topics/14856
#URL : https://web.archive.org/web/20180923221201/http://thebigbrainscompany.com:80/blog/posts/dployer-une-application-symfony-avec-capistrano

#releases/ : chaque sous-répertoire correspondra à un déploiement estampillé avec la date;
#current : est un lien symbolique pointant vers une release spécifique;
#shared/ : les données partagées entre chaque déploiement, par exemple les fichiers de journalisation;
#revisions.log : est l'historique des déploiements;
#repo/ : sera le conteneur de votre repository git, les dossiers de release n'ayant aucune information concernant le système de version.

#Les variables :linked_files & :linked_dirs définissent les éléments que vous souhaitez partager entre chaque releases.