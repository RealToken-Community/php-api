# Load DSL and set up stages
require "capistrano/setup"
require "capistrano/multiconfig"

# Include default deployment tasks
require "capistrano/deploy"
require "capistrano/ssh_doctor"

# Git
require "capistrano/scm/git"
install_plugin Capistrano::SCM::Git

# Git Sub-modules
require "capistrano/scm/git-with-submodules"
install_plugin Capistrano::SCM::Git::WithSubmodules

# Notifications on MacOS
require 'capistrano-nc/nc'

# Symfony
#require 'capistrano/symfony'

#task :require_composer do
#  require 'capistrano/composer'
#end

# Load custom tasks from `lib/capistrano/tasks` if you have any defined
Dir.glob("lib/capistrano/tasks/*.rake").each { |r| import r }