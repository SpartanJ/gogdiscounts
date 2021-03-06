SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;
ALTER TABLE ONLY public.games DROP CONSTRAINT games_pkey;
DROP TABLE public.games;
SET default_tablespace = '';
SET default_with_oids = false;

CREATE TABLE public.games (
    game jsonb,
    id bigint NOT NULL
);

ALTER TABLE ONLY public.games
    ADD CONSTRAINT games_pkey PRIMARY KEY (id);

CREATE TABLE public.games_ar (
    game jsonb,
    id bigint NOT NULL
);

ALTER TABLE ONLY public.games_ar
    ADD CONSTRAINT games_ar_pkey PRIMARY KEY (id);
