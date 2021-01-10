import json
import boto3

def increment_reads(c):
    c=c
    retItem = c.get_item(
        TableName="spotify_stats",
        Key={
            'id':{
                'S' : "reads"
            }
        })
    retItem = int(retItem['Item']['number']['N'])
    # return retItem
    response = c.update_item(
        TableName="spotify_stats",
        Key={
            'id': {
                'S': 'reads',
            }
        },
        AttributeUpdates={
            "number":{
                "Value":{
                    "N" : str(retItem+1)
                    
                }
            }
        })
