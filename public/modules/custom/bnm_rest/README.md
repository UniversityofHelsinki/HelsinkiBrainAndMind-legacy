#BNM rest module

Module contains rest-api related modifications.

Endpoints:

GET /filters: Filters endpoint returns list of objects of 
Parameters: -

Return: Array of affiliate taxonomy term objects
{
    id: int
    name: string
    children: [int]
}
`
 [ 
 { 
    id: 1,
    name: 'affiliate1', 
    childred: [2]
 },
 {
    id: 2,
    name: 'child-affiliate-1,  
    children: []
 }...
 ]
`

GET /search_suggestions
Parameters:
q - string: keyword for autocomplete

example query /search_suggestions?q=uni
return: [string]
`
['unit', 'unity', 'universe', 'university']
`

GET /researchgroup_search
Parameters
q         - string: comma separated list of keywords
affiliate - int: id of of affiliate

Example query /researchgroup_search?q=brain,uni&affiliate=2
Return: array of search results objects

Check the endpoint for full list
