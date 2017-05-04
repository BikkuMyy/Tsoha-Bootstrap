-- CREATE TABLE lauseet
CREATE TABLE Kayttaja 
(
id SERIAL PRIMARY KEY,
kayttajatunnus varchar(20) UNIQUE NOT NULL,
salasana varchar(20) NOT NULL
);

CREATE TABLE Ruoka
(
id SERIAL PRIMARY KEY,
nimi varchar(25) NOT NULL,
kayttokerrat integer,
kommentti varchar(500),
kayttaja integer NOT NULL,
FOREIGN KEY (kayttaja) REFERENCES Kayttaja (id)
);

CREATE TABLE Aines
(
id SERIAL PRIMARY KEY,
nimi varchar(20) NOT NULL UNIQUE
);

CREATE TABLE RuokaAines
(
ruoka integer NOT NULL,
aines integer NOT NULL,
FOREIGN KEY (ruoka) REFERENCES Ruoka (id),
FOREIGN KEY (aines) REFERENCES Aines (id)
);

CREATE TABLE Kategoria
(
id SERIAL PRIMARY KEY,
nimi varchar(20) NOT NULL UNIQUE
);

CREATE TABLE RuokaKategoria
(
ruoka integer NOT NULL,
kategoria integer NOT NULL,
FOREIGN KEY (ruoka) REFERENCES Ruoka (id),
FOREIGN KEY (kategoria) REFERENCES Kategoria (id)
);