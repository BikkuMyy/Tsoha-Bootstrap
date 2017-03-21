-- Testidatan INSERT INTO lauseet

-- Kayttaja
INSERT INTO Kayttaja (nimi, salasana) VALUES('Mymmeli', '12345');
INSERT INTO Kayttaja (nimi, salasana) VALUES('Viljonkka', 'salasana');

-- Kategoria
INSERT INTO Kategoria (nimi) VALUES('Kastike');
INSERT INTO Kategoria (nimi) VALUES('Kanaruoka');
INSERT INTO Kategoria (nimi) VALUES('Uuniruoka');

-- Ruoka
INSERT INTO Ruoka (nimi, kayttokerrat, kommentti, kayttaja) VALUES('Herkkusienikastike', 1, 'Muista pilkkoa sipuli hienoksi', 2);
INSERT INTO Ruoka (nimi, kayttokerrat, kommentti, kayttaja) VALUES('Fried chicken', 1, 'Korppujauhojen sijaan käy myös hapankorppumuru', 1);
INSERT INTO Ruoka (nimi, kayttokerrat, kommentti, kayttaja) VALUES('Makaroonilaatikko', 0, 'Jauhelihan voi korvata soijarouheella', 1);
INSERT INTO Ruoka (nimi, kayttokerrat, kommentti, kayttaja) VALUES('Uunimakkara', 0, 'Muista juusto päälle', 1);

-- RuokaKategoria
INSERT INTO RuokaKategoria VALUES(1, 1);
INSERT INTO RuokaKategoria VALUES(2, 2);
INSERT INTO RuokaKategoria VALUES(3, 3);
INSERT INTO RuokaKategoria VALUES(4, 3);

-- Aines
INSERT INTO Aines (nimi) VALUES('Kana');
INSERT INTO Aines (nimi) VALUES('Herkkusieni');
INSERT INTO Aines (nimi) VALUES('Ranskankerma');
INSERT INTO Aines (nimi) VALUES('Jauheliha');
INSERT INTO Aines (nimi) VALUES('Pasta');

-- RuokaAines
INSERT INTO RuokaAines VALUES(1, 2);
INSERT INTO RuokaAines VALUES(2, 1);
INSERT INTO RuokaAines VALUES(3, 4);
INSERT INTO RuokaAines VALUES(1, 3);
INSERT INTO RuokaAines VALUES(1, 5);
INSERT INTO RuokaAines VALUES(3, 5);