## Coding test

## Problem

Our sales team has just struck a deal with Acme Inc to become the exclusive provider for routing their product shipments via 3rd party trucking
fleets. The catch is that we can only route one shipment to one driver per day.
Each day we get the list of shipment destinations that are available for us to offer to drivers in our network. Fortunately our team of highly trained
data scientists have developed a mathematical model for determining which drivers are best suited to deliver each shipment.
With that hard work done, now all we have to do is implement a program that assigns each shipment destination to a given driver while
maximizing the total suitability of all shipments to all drivers.

The top-secret algorithm is:
- **If the length of the shipment's destination street name is even, the base suitability score (SS) is the number of vowels in the driver’s
name multiplied by 1.5.**
- **If the length of the shipment's destination street name is odd, the base SS is the number of consonants in the driver’s name multiplied by
1.**
- **If the length of the shipment's destination street name shares any common factors (besides 1) with the length of the driver’s name, the
SS is increased by 50% above the base SS.**

Write an application in the language of your choice that assigns shipment destinations to drivers in a way that maximizes the total SS over the set
of drivers. Each driver can only have one shipment and each shipment can only be offered to one driver. Your program should run on the
command line and take as input two newline separated files, the first containing the street addresses of the shipment destinations and the second
containing the names of the drivers. The output should be the total SS and a matching between shipment destinations and drivers. You do not
need to worry about malformed input, but you should certainly handle both upper and lower case names.

## Analysis

The first approach was choose a language to work with, i choose php that is more familarity for me, then i decided to use laravel, becoz laravel has a command line that could be so usefull for this exersice, first problem is determinate what are all the posibles rates for any combination, so first task was made this proccess, then i must identify what are the best combinations, i do some exercises in paper to look how i must generate that combinations array, at first it looks simple, but in the practice i notice that is more complex that i was thinking, so i decide to search for any algoryth that could helps me to get all the combinations on array without repeat a row/column in any pair combine, so i found that this problem represents an assignment problem, and found the hungarian algorymth that is represented by a matrix costs, and there is an solution to find the lowest cost combinations, so with all this information i decide to start coding

## Assumptions

- First was the files will contains the same number of streets and drivers, becoz the algorythm only works on (n)(n) matrix
- That no will be empty names or lines in the files
- That no cares if there are empty spaces between or before the drivers / destinations, so spaces counts

## Approach

- How i said, i choose php to use in this practice, becoz is my main tecnology, and laravel becoz i know how to make command consoles easier 
- The first step was setup a laravel envoirment so i initialize a laravel project, then i need to run a new command on console, so i make that command for artisan.
- The second step is read the inputs, and read the files to get an arrays with both, driver name's and destinations, then use this information to build a matrix with all possibles cost with all possible combinations
- Then used this matrix with a library with the Hungarian algoryth, that's is imported as a helper, but before used it, i converted all values to integers, becoz this library doesnt supports decimal / floats, so i just multiply by 10 all cost values
- When the library returns the result, i just asign to an array all the posible combinations to a human readeable text, and make the return array for the command line, but also i divide by 10 all the cost results, for comeback to normal values all the cost
- Finnally i just iterate the results to show on display

## Sources

- Hungarian algorythm https://en.wikipedia.org/wiki/Hungarian_algorithm
- Hungarian algorythm PHP https://github.com/rpfk/Hungarian

### How to install

- Install composer (https://getcomposer.org)
- Install git (https://git-scm.com/downloads)
- Run the following commands
	git clone https://github.com/Burunito/platform_science.git platform_science
	cd platform_science
	composer install

## Usage
- You can just run the follwing command to execute with the examples doesnt type any path for inputs and will execute with the examples files
	php artisan test:run
- You can make your own list, just remind that must be the same number of lines for destinations and for drivers, any driver / destination separate for a jumpline, just one by line, all that is on a line will be count as one
- The path starts on your root of project, not on public, becoz we are running the command line, so take care of this if u have your files on public, or any other path u must start from the root

## Test

- Just type 
	php artisan test:run
- Then it ask for path of files to import
- Leave it blank just type enter twice
- Then it will show you the score, followed of all the combinations and its individual score

<p align="center">
<img src="https://github.com/Burunito/platform_science/blob/main/public/example.jpg?raw=true" alt="example">
</p>