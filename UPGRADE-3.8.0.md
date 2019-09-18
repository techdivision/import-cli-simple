# Upgrade from 3.7.4 to 3.8.0

## Configuration

### Table Prefix

With the new `--db-table-prefix` parameter, it is possible to pass the table prefix that has been used when the Magento setup created the database. The tabble prefix can either be passed as parameter specified in the configuration at the specific database connection like

```json
{
  ...
  "databases": [
    {
      "id": "live",
      "default" : true,
      "pdo-dsn": "mysql:host=127.0.0.1;dbname=magento2;charset=utf8",
      "username": "magento",
      "password": "foOQNGEcKS8mZmVH",
      "table-prefix": "test_"
    }
  ]
  ...
}
```

whereas the commandline parameter will be preferred if both has been specified.
