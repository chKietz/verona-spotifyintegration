import json
import boto3

def increment_adds(c):
    c=c
    TableName = 'spotify_stats'
    
    retItem = c.get_item(
        TableName="spotify_stats",
        Key={
            'id':{
                'S' : "adds"
            }
        })
    retItem = int(retItem['Item']['number']['N'])
    # return retItem
    response = c.update_item(
        TableName="spotify_stats",
        Key={
            'id': {
                'S': 'adds',
            }
        },
        AttributeUpdates={
            "number":{
                "Value":{
                    "N" : str(retItem+1)
                    
                }
            }
        })

