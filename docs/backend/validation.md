# Validation

All incoming data that is meant to create new entities in database must be validated through JSON-schemas. They are located in `/resources/schemas` folder.

Schema filename must be the same as route name.

Shared types must be placed into `types` subfolder with separate file for each type.