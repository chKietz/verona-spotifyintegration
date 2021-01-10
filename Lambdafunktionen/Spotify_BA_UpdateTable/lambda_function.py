import json
import boto3
from datetime import *
from getItemCount import get_Item_Count 
from getExistingData import get_Existing_Data
from incrementAdds import increment_adds

def lambda_handler(event, context):
    counter = 0
    print("###### EVENT #####")
    print(event)
    c = boto3.client('dynamodb')
    increment_adds(c)
    dateAdded = str(date.today())
    TableName = event[0]['table-name']
    ItemCount = get_Item_Count(c, TableName)
    ExistingData = get_Existing_Data(c, TableName)
    print(ExistingData)
    for item in ExistingData:
        if item['userId']['N'] == str(event[0]['user-id']):
            last_updated = item['dateAdded']["S"]
            date_time_obj = datetime.strptime(last_updated, '%Y-%m-%d')
            time_since_insertion = datetime.now() - date_time_obj
            if time_since_insertion < timedelta(days = 30):
                counter = counter + 1
    if counter == 3:
        return {
            'statusCode': 200,
            'body': json.dumps("Bereits vorhanden")
        }
    
    for item in event:
        print(item)
        artistName = item['artist-name']
        trackTitle = item['track-title']
        userId = str(item['user-id'])
        albumTitle = item['album-title']
        songUrl = item['song-url']
        albumImg = item['album-img']
        c.put_item(
            TableName = 'spotify-ba-user-reads',
            Item={
                'Id' : { 'N' : str(ItemCount + 1) },
                'artistName' : { 'S' : artistName },
                'trackTitle' : { 'S' : trackTitle },
                'albumTitle' : { 'S' : albumTitle},
                'songUrl' : {'S' : songUrl },
                'albumImg' : { 'S' : albumImg },
                'userId' : { 'S' : userId },
                'dateAdded' : {'S' : dateAdded },
            }
        )
        ItemCount = ItemCount + 1
    
    return {
        'statusCode': 200,
        'body': json.dumps('Tracks für ' + userId + ' wurden hinzugefügt.' + str(counter))
    }
