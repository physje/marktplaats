Omdat ik verschillende scripts op mijn server heb draaien die allemaal dezelfde dB-verbinding gebruiken, heb ik de functies en config-files hiervoor allemaal in één map gezet die door alle script gebruikt wordt.
Deze map is dus essentieel voor het functioneren van het marktplaats-script.

Om te zorgen dat het marktplaats-script functioneert moet je de map 'MOVE_THIS_FOLDER' een map hoger verplaatsen en bovendien hernoemen naar 'general_include'.

Dus als je het funda-script op http://www.example.com/scripts/marktplaats/ hebt staan, moet de inhoud van deze folder komen te staan in http://www.example.com/scripts/general_include/