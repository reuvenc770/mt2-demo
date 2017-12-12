# List Profile

## Data Pipeline

### Entity Tables to AWS S3

`S3RedshiftExport` command is scheduled at 1, 13, and 16 minutes past the hour, Sunday through Friday. On Saturday at 0100, there is a rerun that looks farther back. This fires


### Cleanup



## CLI

Command Name | Purpose | Artisan Signature
--- | --- | ---
S3RedshiftExport | This command pushes `S3RedshiftExportJob` jobs to the queue, one for each entity needed in Redshift. |listprofile:dataEtl {--all} {--runtime-threshold=default} {--test-connection-only}
VacuumRedshift | Optimize tables in Redshift | listprofile:optimize {--runtime-threshold=default}

## Jobs

### S3RedshiftExportJob

