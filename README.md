# Content Management System

This is the CrazedSanity Content Management system.

Technically, this isn't actually a CMS... yet.  Right now it's just a framework 
to build a CMS, or any other web application, similar to CakePHP.

## Why Should I Use This?

CS-CMS uses a one-of-a-kind template system.  In this templating system, there 
is *absolutely no logic allowed* in the templates, or "views" (in the context 
of MVC).

## Why Templates?

Templates are a natural way of thinking.  Consider a template in something like 
Microsoft Word: there are placeholders for a client's name, which are replaced 
with real values when used.  That is exactly how CS-CMS uses templates.

One interesting difference is that a template variable can be replaced with 
another template. When building a webpage, a "main" template is created, 
with placeholders (template vars) in place of certain elements, such as a 
header, footer, menu, and the content area: when the page is rendered, these 
elements are replaced with rendered versions of those templates, which could, 
themselves, be built using other template files.

# Technical Stuff

## Naming Schemes

### Class Files

The general syntax of a library file is ```{Name}.{type}.php```. A standard 
class of "foo" would be ```Foo.class.php```.  If that was an abstract class, 
the name would be ```Foo.abstract.php```.  An interface would be 
```Foo.interface.php```.  Interfaces usually have a lowercase "i" prefix, so it 
the class name could also be "iFoo", changing the filename to 
```iFoo.interface.php```, even though it may seem redundant to have both.  The 
interface files should all be held in the "interfaces" sub-folder, which might 
seem triple-redundant.

### Database Schema

A strict syntax should be kept for all database schema, which has many different 
benefits: 
 * standardization
 * easily differentiating tables from views, columns, etc in code
 * extremely simple natural joins

The scheme is as follows: 
 * table: ```{prefix}_{name_of_table}_table```
 * view: ```{prefix}_{name_of_view}_view```

Every table should have a primary key that is unique and non-nullable.  This key 
should, under all but very special circumstances, be an integer.
 
Column naming:
 * generic: {name_of_column}
 * primary key: {name_of_table}_id

Sequences in PostgreSQL (the DBMS that all CS projects are geared for) are 
automatically generated in the form ```{full_table_name}_{primary_key}_seq```.

An important aspect for this (seemingly redundant) naming scheme, using a 
prefix, a meaningful name, is to consider what happens without this. Imagine 
using a loose (or non-existent) standard in a massive code base; the database 
has hundreds of tables, all with a column named "id".  Searching the code for 
references to "id" will turn up hundreds of results.
