**THIS PLUGIN IS NOT MAINTAINED ANYMORE !**

VinceCmsBundle
==============

[![Total Downloads](https://poser.pugx.org/vince/cms-bundle/downloads.png)](https://packagist.org/packages/vince/cms-bundle)
[![Latest Stable Version](https://poser.pugx.org/vince/cms-bundle/v/stable.png)](https://packagist.org/packages/vince/cms-bundle)
[![Build Status](https://travis-ci.org/vincentchalamon/VinceCmsBundle.png?branch=1.0.0)](https://travis-ci.org/vincentchalamon/VinceCmsBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/ac59862d-c431-4d62-b98e-dfb92f331c68/mini.png)](https://insight.sensiolabs.com/projects/ac59862d-c431-4d62-b98e-dfb92f331c68)
[![Coverage Status](https://coveralls.io/repos/vincentchalamon/VinceCmsBundle/badge.png)](https://coveralls.io/r/vincentchalamon/VinceCmsBundle)

Basic CMS features for Symfony 2.5

## TODO

- [ ] I18n
- [ ] Cache APC
- [ ] Cache doctrine
- [ ] Cache HTTP
- [ ] Documentation (README + PHPDoc + GitHub pages)
- [ ] Search configuration (Symfony + ElasticSearch)
- [ ] Search pager: KnpPaginatorBundle
- [ ] Search ajax pager

Documentation
=============

### Installation

- [ ] Install bundle with composer
- [ ] Update AppKernel
<!-- [ ] Install ElasticSearch with composer-->
<!-- [ ] Launch ElasticSearch-->

### Configuration

- [ ] Create override bundle (MyCmsBundle)
- [ ] Create override entities: Article, ArticleMeta, Block, Content, Menu
- [ ] Update config.yml: domain, sitename, tracking_code, model, no_reply, contact

### Fixtures

- [ ] Create fixtures in YML
- [ ] Create templates
- [ ] Create articles
- [ ] Create menus
- [ ] Create blocks

### CMS injection

- [ ] Inject objects (& forms) in template (listeners)
- [ ] Process forms (processors)

### Advanced

- [ ] Override controllers
- [ ] Catch mail on dev (MailCatcher)
- [ ] PHPDoc

Search
======

* Ne dois pas remonter :
    * Système : homepage, accueil, search, rechercher, error
    * Non publié : vincent
    * Pré publié : jordan
    * Pré publié temp : samuel
    * Dépublié : franck
* Doit remonter :
    * Publié : yannick
    * Publié aujourd'hui : benoit
    * Publié jusqu'à aujourd'hui : gilles
    * Publié temporairement : adrien
