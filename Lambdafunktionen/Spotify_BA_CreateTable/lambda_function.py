import json
import boto3

def lambda_handler(event, context):
    TableName = event[0]['table-name']
    client = boto3.client('dynamodb')
    dynamodb = boto3.resource('dynamodb')
    try:
        table = dynamodb.create_table(
            TableName= TableName,
            KeySchema=[
                {
                    'AttributeName': 'Id',
                    'KeyType': 'HASH'
                },
            ],
            AttributeDefinitions=[
                { 
                    'AttributeName' : 'Id',
                    'AttributeType' : 'N'
                }
            ],
            ProvisionedThroughput={
                'ReadCapacityUnits': 10,
                'WriteCapacityUnits': 10
            }
        )
        tableR = client.describe_table(
            TableName = TableName
            )
        response = tableR["Table"]["TableName"] + " erstellt"
    except client.exceptions.ResourceInUseException:
        tableR = client.describe_table(
            TableName = TableName
            )
        response = tableR["Table"]["TableName"] + " vorhanden"
    return response
