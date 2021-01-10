import json
import boto3

def lambda_handler(event, context):
    print("###### EVENT #####")
    print(event)
    client = boto3.client('dynamodb')
    TableName = event['queryStringParameters']['table-name']
    
    response = client.delete_table(
        TableName=TableName
        )
    
    print(response)
    
    return {
        'statusCode': 200,
        'body': json.dumps('Table ' + TableName + ' wurde gelöscht.')
    }
