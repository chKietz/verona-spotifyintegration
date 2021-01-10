import json
import boto3
from getItemCount import get_Item_Count
from incrementReads import increment_reads

def lambda_handler(event, context):

    print(event)
    c = boto3.client('dynamodb')
    increment_reads(c)
    
    # TableName = event['table-name']
    TableName = event['queryStringParameters']['table-name']
    ItemCount = get_Item_Count(c, TableName)
    resDict = {}
    if ItemCount == 0:
        return {
            'statusCode': 200,
            'body': "Keine Ergebnisse"
        }

    for i in range (1, int(ItemCount)+1):
        resDict[int(i-1)] = {}
        r = c.get_item(
            TableName = TableName,
            Key={
                'Id' : { 'N' : str(i)},
            })
        resDict[int(i-1)]["artistName"] = {}
        resDict[int(i-1)]["artistName"] = r["Item"]["artistName"]["S"]
        resDict[int(i-1)]["trackTitle"] = {}
        resDict[int(i-1)]["trackTitle"] = r["Item"]["trackTitle"]["S"]
        resDict[int(i-1)]["userId"] = {}
        resDict[int(i-1)]["userId"] = r["Item"]["userId"]["S"]
        resDict[int(i-1)]["albumTitle"] = {}
        resDict[int(i-1)]["albumTitle"] = r["Item"]["albumTitle"]["S"]
        resDict[int(i-1)]["songUrl"] = {}
        resDict[i-1]["songUrl"] = r["Item"]["songUrl"]["S"]
        resDict[i-1]["albumImg"] = {}
        resDict[i-1]["albumImg"] = r["Item"]["albumImg"]["S"]

    return {
        'statusCode': 200,
        'body': json.dumps(resDict)
    }
