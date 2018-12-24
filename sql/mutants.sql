create database mutants;

create table mutants.verification
(
  id         int auto_increment
    primary key,
  dna        text       null,
  hash       char(32)   null,
  is_mutant  tinyint(1) null,
  updated_at timestamp  null,
  tries      int        null,
  created_at timestamp  not null
);