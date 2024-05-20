# Full Stack Assessment:

## Backend Part

1. Create a CRUD API for managing `users` table created by the following SQL statement:

```sql
CREATE TABLE users (
    ID INT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    created_at TIMESTAMP
);
```

To speed up the development we recommend using the API platform for Symfony (we don't work with Laravel so cannot suggest more).

2. Extend the user GET handler to support filtering by field value // DONE
3. Implement simple sorting by field // DONE
4. Pagination // DONE

### Technical requirements:

-   Framework: Symfony / Laravel // Laravel Used
-   ORM: Doctrine / Eloquent // Eloquent
-   DB: MySQL / Postgresql // MYSQL

## Frontent Part

1. Bind to CRUD API and display the user's list // DONE
2. Adding / Modify users should be possible by completing the form inside a modal // DONE , need to fix issue
3. Deleting user should require confirmation modal // DONE need to fix issue
4. Sorting by arbitrary column (think how to pass the sorting order information) // DONE
5. Quick search // DONE
6. Keep the search filters / sorting persistent between reloads // DONE

### Technical requirements/stack:

1. Vue.js 3.x / Nuxt3
2. The project should be launched from the console
3. Styles should be built with SASS
4. Images should have optimization provided
5. We will check the Lighthouse score
6. The project should be provided with .zip / .tar.gz
7. Node package install should be possible with the latest npm/node version or please provide a proper readme file with requirements
