# Backup all GitLab repositories to Synology NAS

A simple script to backup all your GitLab repositories to Synology NAS.

## Requirements

- PHP ≥ 5.6

## Create an Access Token

On gitlab.com, go to your profile -> Settings -> Access Tokens and create a new Access Token

**Name**: _backup_ (it doesn't matter)
**Expires at**: tomorrow (or later, if you want to reuse it)
**Scopes**: _read_api_ & _read_repository_

Then click on Create personal access token.

## Run the backup script manually

After the token is generated, paste it in the script and set destination directory.

```bash
php backup-gitlab-repositories-to-synology.php
```

## Create a scheduled task on Synology

[See documentation](https://www.synology.com/en-uk/knowledgebase/DSM/help/DSM/AdminCenter/system_taskscheduler)

## References

[Gitlab API Documentation](https://docs.gitlab.com/ee/api/)

[Original Script](https://gist.github.com/roydejong/165c72a87da332d1c34b2ec486a7e5bd)
