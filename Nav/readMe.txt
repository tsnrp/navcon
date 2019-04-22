This is the root folder for the TSN Stellar Navigation Console

Change Log
2018.10.18 Matsiyan
- Updated the gateNetwork.png for the S10 Sandbox expansion
- Added what is known of Arcturus separately for Public and Intel
- Applied Starry's updates
  - Drop down menu now has two pages
  - Navigate to systems by clicking on them in the overview
2018.09.12 Matsiyan
- Copy of the files currently deployed to http://www.1sws.com/Intel/Nav
- Recently updated Waypoints 52, 60, 89, Arietis, Atlantis and Euphini Expanse

Development Backlog To-Do
- Reverse the sector numbering in Euphini Expanse 
  a) change the sector image file names, 
  b) update the entities.txt accordingly, and 
  c) remove the invertVertical logic from index.php
- Consider separate copies for Public and Intel consumption

Documentation: How It Works / How To Make Changes 

The Nav Console displays a Systems menu item for every subfolder under the Nav/sectors folder and displays 
Each sector folder contains 
1) sector.txt defining the number of sectors across and down in the defined system (The sectors folder should really be named systems.)
2) a png image file of each sector in the system. Each is named with the the sequential number defining its position in the sector starting top left and proceeding across and down.
3) entities.txt listing all stations and other objects on the map.
- Each line lists the name, type, and sector-map-number defining the object. 
- Each line is parsed and displayed by Nav/sectorEntities.php. This should be updated for new entity types e.g. Planet
4) mainMapPos.txt defining the number of pixels in from the left and down from the top of the graphic in the img/gateNetwork.png overview map
   index.php uses it to locate the clickzones to navigate from the network map to a sector