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