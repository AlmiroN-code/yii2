 # Implementation Plan

- [x] 1. Setup Tailwind CSS via npm





  - [x] 1.1 Initialize npm and install Tailwind dependencies

    - Create package.json with tailwindcss, postcss, autoprefixer
    - Create tailwind.config.js with content paths for views
    - Create postcss.config.js
    - _Requirements: 6.1, 6.2_
  - [x] 1.2 Create source CSS files and build scripts


    - Create src/css/app.css with Tailwind directives
    - Create src/css/admin.css with Tailwind directives
    - Add npm scripts for dev (watch) and build (minify)
    - _Requirements: 6.1, 6.3_

- [x] 2. Create database migrations





  - [x] 2.1 Create Category migration

    - Fields: id, parent_id, name, slug, description, sort_order, timestamps
    - Add foreign key for parent_id self-reference
    - _Requirements: 2.1_

  - [x] 2.2 Create Tag migration

    - Fields: id, name, slug, created_at
    - Add unique index on slug
    - _Requirements: 3.1_
  - [x] 2.3 Create Publication migration


    - Fields: id, category_id, title, slug, excerpt, content, featured_image, status, meta fields, timestamps
    - Add foreign key for category_id


    - _Requirements: 1.1_
  - [x] 2.4 Create PublicationTag junction migration




    - Fields: publication_id, tag_id (composite primary key)
    - Add foreign keys with CASCADE delete
    - _Requirements: 3.2_

- [x] 3. Create ActiveRecord models





  - [x] 3.1 Create Category model


    - Define tableName, rules, attributeLabels
    - Add relations: getParent(), getChildren(), getPublications()
    - Implement slug generation in beforeSave
    - _Requirements: 2.1, 2.2_
  - [x] 3.2 Write property test for Category model


    - **Property 4: Category deletion preserves publications**
    - **Validates: Requirements 2.3**
  - [x] 3.3 Create Tag model


    - Define tableName, rules, attributeLabels
    - Add relation: getPublications() via junction
    - Implement slug generation in beforeSave
    - _Requirements: 3.1_
  - [x] 3.4 Write property test for Tag model

    - **Property 5: Tag many-to-many integrity**
    - **Validates: Requirements 3.2, 3.3**

  - [x] 3.5 Create Publication model

    - Define tableName, rules, attributeLabels
    - Add relations: getCategory(), getTags()
    - Implement tag assignment via setTagIds()
    - _Requirements: 1.1, 1.2_

  - [x] 3.6 Write property test for Publication slug uniqueness
    - **Property 2: Slug uniqueness**
    - **Validates: Requirements 1.5**
  - [x] 3.7 Write property test for Publication visibility

    - **Property 3: Publication visibility by status**
    - **Validates: Requirements 1.3, 1.4**


  - [x] 3.8 Create PublicationTag junction model
    - Define composite primary key
    - _Requirements: 3.2_

- [x] 4. Create SlugService


  - [x] 4.1 Implement SlugService class
    - Method generate(title, table, excludeId) for unique slug creation
    - Transliteration support for Cyrillic
    - _Requirements: 1.5, 7.2_
  - [x] 4.2 Write unit tests for SlugService

    - Test slug generation, uniqueness, special characters
    - _Requirements: 1.5_

- [x] 5. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Create Admin module structure






  - [x] 6.1 Create admin module with Module class

    - Create modules/admin/Module.php
    - Register module in config/web.php
    - _Requirements: 5.1_
  - [x] 6.2 Create admin layout with Tailwind


    - Create modules/admin/views/layouts/main.php
    - Include navigation, flash messages
    - _Requirements: 5.4_
  - [x] 6.3 Create AuthController for admin login/logout


    - Implement actionLogin, actionLogout
    - Create login view with Tailwind form
    - _Requirements: 5.1, 5.5_
  - [x] 6.4 Create DefaultController for dashboard


    - Display statistics: publications count, categories count, tags count
    - Create dashboard view
    - _Requirements: 5.2_

- [x] 7. Create Admin CRUD controllers




  - [x] 7.1 Create admin/PublicationController


    - Implement index, create, update, delete actions
    - Handle tag assignment in forms
    - Handle image upload
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 5.3_
  - [x] 7.2 Create publication admin views

    - index.php with data table
    - _form.php with all fields including tag checkboxes
    - _Requirements: 5.3_

  - [x] 7.3 Create admin/CategoryController

    - Implement index, create, update, delete actions
    - Handle parent category selection
    - _Requirements: 2.1, 5.3_
  - [x] 7.4 Create category admin views

    - index.php with hierarchical display
    - _form.php with parent dropdown
    - _Requirements: 5.3_

  - [x] 7.5 Create admin/TagController

    - Implement index, create, update, delete actions
    - _Requirements: 3.1, 5.3_

  - [x] 7.6 Create tag admin views
    - index.php with data table
    - _form.php with name field
    - _Requirements: 5.3_

- [x] 8. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.

- [x] 9. Create Frontend controllers and views






  - [x] 9.1 Create frontend layout with Tailwind

    - Create views/layouts/main.php with header, sidebar, footer
    - Include category navigation and tag cloud
    - _Requirements: 4.5, 6.4_
  - [x] 9.2 Create PublicationController for frontend


    - actionIndex: paginated list of published publications
    - actionView: single publication by slug
    - _Requirements: 4.1, 4.2_
  - [x] 9.3 Create publication frontend views


    - index.php with publication cards grid
    - view.php with full content, category, tags
    - _Requirements: 4.1, 4.2_
  - [x] 9.4 Create CategoryController for frontend


    - actionView: publications filtered by category slug
    - _Requirements: 4.3_
  - [x] 9.5 Create category frontend view


    - view.php with filtered publications list
    - _Requirements: 4.3_
  - [x] 9.6 Create TagController for frontend


    - actionView: publications filtered by tag slug
    - _Requirements: 4.4_

  - [x] 9.7 Create tag frontend view

    - view.php with filtered publications list
    - _Requirements: 4.4_

- [x] 10. Create widgets





  - [x] 10.1 Create CategoryTreeWidget


    - Render hierarchical category list
    - _Requirements: 2.4_

  - [x] 10.2 Create TagCloudWidget

    - Render tag cloud with usage counts
    - _Requirements: 3.4_

- [x] 11. Configure URL rules






  - [x] 11.1 Add pretty URL rules in config/web.php

    - Enable urlManager with pretty URLs
    - Add rules for publication, category, tag slugs
    - _Requirements: 7.2_

- [x] 12. Implement SEO features





  - [x] 12.1 Add meta tags to publication view


    - Set page title, meta description, Open Graph tags
    - _Requirements: 7.1_
  - [x] 12.2 Add lazy loading for images


    - Add loading="lazy" attribute to images
    - _Requirements: 7.3_
-

- [x] 13. Final Checkpoint - Ensure all tests pass




  - Ensure all tests pass, ask the user if questions arise.
