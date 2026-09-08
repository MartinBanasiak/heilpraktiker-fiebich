# README #

### What is this repository for? ###

* This repository containes the Code for the dcShop and dcCMS solutions from dynamic commerce GmbH
* The code in this version is interfaced with and integrated in Microsoft Dynamics NAV 2016


### Prerequisite Software Installations ###

These are the software requirements for using the web-side application

* Have PHP in PATH
* Have composer installed
* Have git installed (git-scm for windows, with option to execute git commands from default command prompt)
* Have SSH setup (should work automatically after git installation)
* If not already present, create an ssh-key for the user intended to handle deployment as with 4096-bit rsa encryption. Chose default name (id_rsa)
* Have a virtualization-software compatible with Vagrant installed (tested with Oracle VirtualBox)
* Have Vagrant installed



### Initial Configuration, Provisioning & Deployment ###

This is detailed list of all the steps needed to configure, provision and deploy this solution for the first time.
After initial setup, future deployments and rollbacks can be handled with a single command.

1. Have the absolute path to the desired project-root directory ready
2. Have id_rsa.pub SSH-key of script user configured as deployment key for this repo
3. Have id_rsa.pub SSH-key of script user configured as deployment key for repo 'provisioning_deployment'
4. Have full read and write permissions on project-root as aforementioned script user
5. Open terminal (Admin Console required on Windows in order to handle symlinks)
6. Create a directory for the provisioning & deployment tools
7. Clone the prodeploy app's repository into the directory
8. Checkout the desired branch
9. Modify or copy-and-modify the project_config in config-subdirectory of provisioning & deployment tools for project and local machine/user
10. Have the absolute path to the modified config-file ready
11. From terminal, setup directories and copy config by executing `php [path/to/prodeploy/src/app/]prodeploy.php makedirs [/absolute/project/path] [/absolute/path/to/project_config.yaml]`
13. Initialize bitbucket-linked git-repository for deploying MysydeShop / dc-one by executing `php [path/to/prodeploy/src/app/]prodeploy.php initgit [/absolute/project/path]`
15. Deploy the latest commit by executing `php [path/to/prodeploy/src/app/]prodeploy.php deploy-commit [/absolute/project/path] [[commit-hash]]`


### Deployments & Rollbacks ###

(These operations have to be performed as a user with full read and write permissions to the project-root and everything contained in it. On Windows systems, an Admin Console is required in order to handle symlinks)

* After initial configuration, provisioning and deployment, all future deployments and rollbacks are a matter of executing the `deploy-commit` command of the `prodeploy` app.
* The system automatically retains a settable number of commits. Switching between retained versions only switches symlinks, and is thus atomic and near-instantaneous
* To switch the active version to the latest commit, simply execute `php [path/to/prodeploy/src/app/]prodeploy.php deploy-commit [/absolute/project/path]`
* To switch the active version to any commit of the branch set for deployment, simply execute `php [path/to/prodeploy/src/app/]prodeploy.php deploy-commit [/absolute/project/path] [[commit-hash]]`
* You can abbreviate the sha1-hash identifying the commit as much as you like, as long as it is unique (the program resolves the hash into its full version)


### Access and Contribution Guidelines ###

* Only members of the dynamic commerce Innovation Lab team can push to this repository and grant permissions to it
* To have deployment-keys registered in order gain access to dcShop, dcCMS and their provisioning & deployment tools, please contact a member of the Innovation Lab Team
* Pull-requests may be submitted by technical employees of dynamic commerce GmbH