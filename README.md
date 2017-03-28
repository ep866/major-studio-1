##Major Studio 1

<br>
####WORKSHOPS:

#####Workshop 1  
_01/26/2017_

* Added index.html with exercises 

Setup and link to github.io

[https://ep866.github.io/](https://ep866.github.io/)

#####Workshop 2  
_01/31/2017_

* This is my p5 game. Counts the mouse moves on X and Y coordinates. Visualizes them as a bar chart and awards points if the number of X moves is equal to the number of Y moves. The game restarts when the user reaches the end of the window width. There is essentially no way to win the game and this is by design - I wanted to create the most annoying game. The logic for winning can be that at scoring 100 points the user wins the game. A timer can be added that awards the user additional points for speed and makes it meaningful to play multiple times to improve one's score. This can be done easily with two additional conditionals but then the game won't be annoying anymore.    

Here is the live version of the game: 

[https://ep866.github.io/p5_game/
](https://ep866.github.io/p5_game/)

#####Workshop 3  
_02/02/2017_

* Load and Visualize data in p5

<br>
<br>
####PROJECTS:


#### Data Review 
#####1. Selected Data Sources  

* The topic I am interested in is gender dynamics of the city and how that correlates with different variables. Bellow are the three datasets I found interesting and related to the subject and I also started exploring the data in QGIS to confirm if there is variability to draw conclusions. 

* Disaggregated Demographic Manhattan
_(US Census Block level data?)_

	* I am interested in the female vs male population distribution per block in conjunction with age (possibly median age or more granular). As additional layers I can look at distribution by race. I think it would be interesting to look at this in conjunction with income at a latter date.  
Question: Did anyone get what the source of this dataset is? As far as I could tell it is coming from the census block level data but I could not confirm as the source was not cited.

* MapPLUTO
	* This is one of the richest datasets that the city is making available. I am specifically interested in the land use category, number of floors, year built, units residential and not-residential features from PLUTO. This will be interesting to look at in the context of 3D map tiles. 

* US CENSUS block level data NYC
	* I would like to investigate more in depth the tenure versus vacancy status of units (h4,h5) and correlate these with gender, race, and/or size of household. Locals have some intuition of what the market distribution looks like as well as race distribution but definitely not gender distribution. I think it will be valuable to learn from the dataset what this landscape looks like in reality. 
 <br>

#####2. Supplemental Sources

   - Additional data such as ancestry, language spoken at home, foreign birth region, us citizenship status, and education
[https://factfinder.census.gov/faces/tableservices/jsf/pages/productview.xhtml?src=bkmk ](https://factfinder.census.gov/faces/tableservices/jsf/pages/productview.xhtml?src=bkmk )

  - Social media and inequality in NYC case study. This is informative as it uses recent data and has the benefit of “real-time” reporting. 
[http://www.citylab.com/tech/2016/07/what-instagram-reveals-about-inequality-in-new-york-city/493046/](http://www.citylab.com/tech/2016/07/what-instagram-reveals-about-inequality-in-new-york-city/493046/)

  - Income and race: comparing cities. Good case study for multivariate geospatial visualizations.
[http://www.businessinsider.com/income-and-racial-inequality-maps-2015-5](http://www.businessinsider.com/income-and-racial-inequality-maps-2015-5)  


#####3. Questions

* Have you found any evidence of correlation between the ethnic or gender makeup of neighborhoods and risk variables such as risk of flooding or pollution? For example, hispanics or black people tend to live in areas that are flood zones? Or female householders tend to live in most polluted areas in NYC?
 
* Have you looked at gender in relation to your research? As an example, the Greenpoint case study in the wicked problems article, could gender play similar role to race, in identifying potential sources and solutions of the problem?

* Is it fair to speculate that New York City’s population density and constantly changing dynamic (many people rent and come and go rather than settle) contributes to the makeup of neighborhoods? In other words, could I fairly assume that inequality can be measured by proxy of rent, owned vs vacant units?


### Project 1 Final
[https://ep866.github.io/census/](https://ep866.github.io/census/)


### Final Projects proposals

##### Income and sexI am studying the relationship/correlation between median household income and sex distribution at the block group level in New York City. The dataset I am using is the American Community Survey. At the completion of the project we will be able to answer questions such as what is the sex distribution per block group in New York City? Are there outliers with unusual concentration of women or men? What are the block groups with the lowest income for females versus males? What are the neighborhoods with highest/lowest concentration of female/male and what is their income? Are there significant differences in income based on sex for individual block groups, i.e. are women and men living in the most expensive neighborhoods equally affluent? Contingent on time and scope the data may be further augmented to allow for some inference, with detailed breakdown of education level and age distribution. The research question is relevant as it allow us to look at the city trough the lens of sex rather than race, which is the most popular unit of analysis, used. This will potentially uncover a new or an additional way of describing local communities. Visualization makes a difference, as at that level of granularity (block group) a table or spreadsheet will obscure the majority of the potential findings. Visualization will allow for quick pattern recognition and make the extraction of useful findings both possible and more efficient. ##### Occupation and sexI am visualizing the civilian employed population by sex in NYC. The geographical unit is ZCTA5, the census approximation of a zip code. The dataset is the American Community Survey. This means looking at 36 groups of occupations and the number of males versus females employed in each. Examples of occupations are “Business and financial operations”, “legal”, “computer and mathematical”, “art, design, entertainment, sports, and media.” This is a way of looking at NYC through the prism of what people living here do. The visualization will allow us to see if there are/what are the differences in occupations for males versus females. We can answer questions such as how many people are employed in the managerial versus service industry in each zip and of what sex they are. We will be able to detect segregation in communities based on occupation as well as gender bias. I expect to see the geography of the city change drastically based on what occupation is selected with the service industry specifically being pushed out to the margins of the boroughs and high concentration of higher earners in the city There is only one way to confirm if that is the case and it is by visualizing it. The question is both socially relevant and interesting in my opinion. Similarly as above, the visualization will enable users greatly to perform quick pattern discovery that is not possible in table format. ##### Marital status and sexUsing the same dataset I would like to look at marital status at the block level. I will break down the data by both sex and race. I just find this to be a potentially very interesting visualization. It does not make the same research contribution as the other two but it is still socially relevant. Unlike in the previous two cases this one does not need to be visualized on a map, as the relationship with geography is mostly irrelevant. 




