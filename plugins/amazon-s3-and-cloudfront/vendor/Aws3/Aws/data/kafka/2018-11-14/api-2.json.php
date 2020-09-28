<?php

// This file was auto-generated from sdk-root/src/data/kafka/2018-11-14/api-2.json
return ['metadata' => ['apiVersion' => '2018-11-14', 'endpointPrefix' => 'kafka', 'signingName' => 'kafka', 'serviceFullName' => 'Managed Streaming for Kafka', 'serviceAbbreviation' => 'Kafka', 'serviceId' => 'Kafka', 'protocol' => 'rest-json', 'jsonVersion' => '1.1', 'uid' => 'kafka-2018-11-14', 'signatureVersion' => 'v4'], 'operations' => ['CreateCluster' => ['name' => 'CreateCluster', 'http' => ['method' => 'POST', 'requestUri' => '/v1/clusters', 'responseCode' => 200], 'input' => ['shape' => 'CreateClusterRequest'], 'output' => ['shape' => 'CreateClusterResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'UnauthorizedException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'ConflictException']]], 'CreateConfiguration' => ['name' => 'CreateConfiguration', 'http' => ['method' => 'POST', 'requestUri' => '/v1/configurations', 'responseCode' => 200], 'input' => ['shape' => 'CreateConfigurationRequest'], 'output' => ['shape' => 'CreateConfigurationResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'UnauthorizedException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'ConflictException']]], 'DeleteCluster' => ['name' => 'DeleteCluster', 'http' => ['method' => 'DELETE', 'requestUri' => '/v1/clusters/{clusterArn}', 'responseCode' => 200], 'input' => ['shape' => 'DeleteClusterRequest'], 'output' => ['shape' => 'DeleteClusterResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'DescribeCluster' => ['name' => 'DescribeCluster', 'http' => ['method' => 'GET', 'requestUri' => '/v1/clusters/{clusterArn}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeClusterRequest'], 'output' => ['shape' => 'DescribeClusterResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'DescribeClusterOperation' => ['name' => 'DescribeClusterOperation', 'http' => ['method' => 'GET', 'requestUri' => '/v1/operations/{clusterOperationArn}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeClusterOperationRequest'], 'output' => ['shape' => 'DescribeClusterOperationResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'DescribeConfiguration' => ['name' => 'DescribeConfiguration', 'http' => ['method' => 'GET', 'requestUri' => '/v1/configurations/{arn}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeConfigurationRequest'], 'output' => ['shape' => 'DescribeConfigurationResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException'], ['shape' => 'NotFoundException'], ['shape' => 'ServiceUnavailableException']]], 'DescribeConfigurationRevision' => ['name' => 'DescribeConfigurationRevision', 'http' => ['method' => 'GET', 'requestUri' => '/v1/configurations/{arn}/revisions/{revision}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeConfigurationRevisionRequest'], 'output' => ['shape' => 'DescribeConfigurationRevisionResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException'], ['shape' => 'NotFoundException'], ['shape' => 'ServiceUnavailableException']]], 'GetBootstrapBrokers' => ['name' => 'GetBootstrapBrokers', 'http' => ['method' => 'GET', 'requestUri' => '/v1/clusters/{clusterArn}/bootstrap-brokers', 'responseCode' => 200], 'input' => ['shape' => 'GetBootstrapBrokersRequest'], 'output' => ['shape' => 'GetBootstrapBrokersResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ConflictException'], ['shape' => 'ForbiddenException']]], 'ListClusterOperations' => ['name' => 'ListClusterOperations', 'http' => ['method' => 'GET', 'requestUri' => '/v1/clusters/{clusterArn}/operations', 'responseCode' => 200], 'input' => ['shape' => 'ListClusterOperationsRequest'], 'output' => ['shape' => 'ListClusterOperationsResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'UnauthorizedException'], ['shape' => 'ForbiddenException']]], 'ListClusters' => ['name' => 'ListClusters', 'http' => ['method' => 'GET', 'requestUri' => '/v1/clusters', 'responseCode' => 200], 'input' => ['shape' => 'ListClustersRequest'], 'output' => ['shape' => 'ListClustersResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'UnauthorizedException'], ['shape' => 'ForbiddenException']]], 'ListConfigurationRevisions' => ['name' => 'ListConfigurationRevisions', 'http' => ['method' => 'GET', 'requestUri' => '/v1/configurations/{arn}/revisions', 'responseCode' => 200], 'input' => ['shape' => 'ListConfigurationRevisionsRequest'], 'output' => ['shape' => 'ListConfigurationRevisionsResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException'], ['shape' => 'NotFoundException'], ['shape' => 'ServiceUnavailableException']]], 'ListConfigurations' => ['name' => 'ListConfigurations', 'http' => ['method' => 'GET', 'requestUri' => '/v1/configurations', 'responseCode' => 200], 'input' => ['shape' => 'ListConfigurationsRequest'], 'output' => ['shape' => 'ListConfigurationsResponse'], 'errors' => [['shape' => 'ServiceUnavailableException'], ['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'ListKafkaVersions' => ['name' => 'ListKafkaVersions', 'http' => ['method' => 'GET', 'requestUri' => '/v1/kafka-versions', 'responseCode' => 200], 'input' => ['shape' => 'ListKafkaVersionsRequest'], 'output' => ['shape' => 'ListKafkaVersionsResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'ListNodes' => ['name' => 'ListNodes', 'http' => ['method' => 'GET', 'requestUri' => '/v1/clusters/{clusterArn}/nodes', 'responseCode' => 200], 'input' => ['shape' => 'ListNodesRequest'], 'output' => ['shape' => 'ListNodesResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'ListTagsForResource' => ['name' => 'ListTagsForResource', 'http' => ['method' => 'GET', 'requestUri' => '/v1/tags/{resourceArn}', 'responseCode' => 200], 'input' => ['shape' => 'ListTagsForResourceRequest'], 'output' => ['shape' => 'ListTagsForResourceResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException']]], 'TagResource' => ['name' => 'TagResource', 'http' => ['method' => 'POST', 'requestUri' => '/v1/tags/{resourceArn}', 'responseCode' => 204], 'input' => ['shape' => 'TagResourceRequest'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException']]], 'UntagResource' => ['name' => 'UntagResource', 'http' => ['method' => 'DELETE', 'requestUri' => '/v1/tags/{resourceArn}', 'responseCode' => 204], 'input' => ['shape' => 'UntagResourceRequest'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'InternalServerErrorException']]], 'UpdateBrokerCount' => ['name' => 'UpdateBrokerCount', 'http' => ['method' => 'PUT', 'requestUri' => '/v1/clusters/{clusterArn}/nodes/count', 'responseCode' => 200], 'input' => ['shape' => 'UpdateBrokerCountRequest'], 'output' => ['shape' => 'UpdateBrokerCountResponse'], 'errors' => [['shape' => 'ServiceUnavailableException'], ['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'UpdateBrokerStorage' => ['name' => 'UpdateBrokerStorage', 'http' => ['method' => 'PUT', 'requestUri' => '/v1/clusters/{clusterArn}/nodes/storage', 'responseCode' => 200], 'input' => ['shape' => 'UpdateBrokerStorageRequest'], 'output' => ['shape' => 'UpdateBrokerStorageResponse'], 'errors' => [['shape' => 'ServiceUnavailableException'], ['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]], 'UpdateClusterConfiguration' => ['name' => 'UpdateClusterConfiguration', 'http' => ['method' => 'PUT', 'requestUri' => '/v1/clusters/{clusterArn}/configuration', 'responseCode' => 200], 'input' => ['shape' => 'UpdateClusterConfigurationRequest'], 'output' => ['shape' => 'UpdateClusterConfigurationResponse'], 'errors' => [['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException'], ['shape' => 'NotFoundException'], ['shape' => 'ServiceUnavailableException']]], 'UpdateMonitoring' => ['name' => 'UpdateMonitoring', 'http' => ['method' => 'PUT', 'requestUri' => '/v1/clusters/{clusterArn}/monitoring', 'responseCode' => 200], 'input' => ['shape' => 'UpdateMonitoringRequest'], 'output' => ['shape' => 'UpdateMonitoringResponse'], 'errors' => [['shape' => 'ServiceUnavailableException'], ['shape' => 'BadRequestException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException'], ['shape' => 'ForbiddenException']]]], 'shapes' => ['BadRequestException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 400]], 'BrokerAZDistribution' => ['type' => 'string', 'enum' => ['DEFAULT']], 'BrokerEBSVolumeInfo' => ['type' => 'structure', 'members' => ['KafkaBrokerNodeId' => ['shape' => '__string', 'locationName' => 'kafkaBrokerNodeId'], 'VolumeSizeGB' => ['shape' => '__integer', 'locationName' => 'volumeSizeGB']], 'required' => ['VolumeSizeGB', 'KafkaBrokerNodeId']], 'BrokerLogs' => ['type' => 'structure', 'members' => ['CloudWatchLogs' => ['shape' => 'CloudWatchLogs', 'locationName' => 'cloudWatchLogs'], 'Firehose' => ['shape' => 'Firehose', 'locationName' => 'firehose'], 'S3' => ['shape' => 'S3', 'locationName' => 's3']]], 'BrokerNodeGroupInfo' => ['type' => 'structure', 'members' => ['BrokerAZDistribution' => ['shape' => 'BrokerAZDistribution', 'locationName' => 'brokerAZDistribution'], 'ClientSubnets' => ['shape' => '__listOf__string', 'locationName' => 'clientSubnets'], 'InstanceType' => ['shape' => '__stringMin5Max32', 'locationName' => 'instanceType'], 'SecurityGroups' => ['shape' => '__listOf__string', 'locationName' => 'securityGroups'], 'StorageInfo' => ['shape' => 'StorageInfo', 'locationName' => 'storageInfo']], 'required' => ['ClientSubnets', 'InstanceType']], 'BrokerNodeInfo' => ['type' => 'structure', 'members' => ['AttachedENIId' => ['shape' => '__string', 'locationName' => 'attachedENIId'], 'BrokerId' => ['shape' => '__double', 'locationName' => 'brokerId'], 'ClientSubnet' => ['shape' => '__string', 'locationName' => 'clientSubnet'], 'ClientVpcIpAddress' => ['shape' => '__string', 'locationName' => 'clientVpcIpAddress'], 'CurrentBrokerSoftwareInfo' => ['shape' => 'BrokerSoftwareInfo', 'locationName' => 'currentBrokerSoftwareInfo'], 'Endpoints' => ['shape' => '__listOf__string', 'locationName' => 'endpoints']]], 'BrokerSoftwareInfo' => ['type' => 'structure', 'members' => ['ConfigurationArn' => ['shape' => '__string', 'locationName' => 'configurationArn'], 'ConfigurationRevision' => ['shape' => '__long', 'locationName' => 'configurationRevision'], 'KafkaVersion' => ['shape' => '__string', 'locationName' => 'kafkaVersion']]], 'ClientAuthentication' => ['type' => 'structure', 'members' => ['Tls' => ['shape' => 'Tls', 'locationName' => 'tls']]], 'ClientBroker' => ['type' => 'string', 'enum' => ['TLS', 'TLS_PLAINTEXT', 'PLAINTEXT']], 'CloudWatchLogs' => ['type' => 'structure', 'members' => ['Enabled' => ['shape' => '__boolean', 'locationName' => 'enabled'], 'LogGroup' => ['shape' => '__string', 'locationName' => 'logGroup']], 'required' => ['Enabled']], 'ClusterInfo' => ['type' => 'structure', 'members' => ['ActiveOperationArn' => ['shape' => '__string', 'locationName' => 'activeOperationArn'], 'BrokerNodeGroupInfo' => ['shape' => 'BrokerNodeGroupInfo', 'locationName' => 'brokerNodeGroupInfo'], 'ClientAuthentication' => ['shape' => 'ClientAuthentication', 'locationName' => 'clientAuthentication'], 'ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'ClusterName' => ['shape' => '__string', 'locationName' => 'clusterName'], 'CreationTime' => ['shape' => '__timestampIso8601', 'locationName' => 'creationTime'], 'CurrentBrokerSoftwareInfo' => ['shape' => 'BrokerSoftwareInfo', 'locationName' => 'currentBrokerSoftwareInfo'], 'LoggingInfo' => ['shape' => 'LoggingInfo', 'locationName' => 'loggingInfo'], 'CurrentVersion' => ['shape' => '__string', 'locationName' => 'currentVersion'], 'EncryptionInfo' => ['shape' => 'EncryptionInfo', 'locationName' => 'encryptionInfo'], 'EnhancedMonitoring' => ['shape' => 'EnhancedMonitoring', 'locationName' => 'enhancedMonitoring'], 'NumberOfBrokerNodes' => ['shape' => '__integer', 'locationName' => 'numberOfBrokerNodes'], 'OpenMonitoring' => ['shape' => 'OpenMonitoring', 'locationName' => 'openMonitoring'], 'State' => ['shape' => 'ClusterState', 'locationName' => 'state'], 'Tags' => ['shape' => '__mapOf__string', 'locationName' => 'tags'], 'ZookeeperConnectString' => ['shape' => '__string', 'locationName' => 'zookeeperConnectString']]], 'ClusterOperationInfo' => ['type' => 'structure', 'members' => ['ClientRequestId' => ['shape' => '__string', 'locationName' => 'clientRequestId'], 'ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'CreationTime' => ['shape' => '__timestampIso8601', 'locationName' => 'creationTime'], 'EndTime' => ['shape' => '__timestampIso8601', 'locationName' => 'endTime'], 'ErrorInfo' => ['shape' => 'ErrorInfo', 'locationName' => 'errorInfo'], 'OperationArn' => ['shape' => '__string', 'locationName' => 'operationArn'], 'OperationState' => ['shape' => '__string', 'locationName' => 'operationState'], 'OperationType' => ['shape' => '__string', 'locationName' => 'operationType'], 'SourceClusterInfo' => ['shape' => 'MutableClusterInfo', 'locationName' => 'sourceClusterInfo'], 'TargetClusterInfo' => ['shape' => 'MutableClusterInfo', 'locationName' => 'targetClusterInfo']]], 'ClusterState' => ['type' => 'string', 'enum' => ['ACTIVE', 'CREATING', 'UPDATING', 'DELETING', 'FAILED']], 'Configuration' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'locationName' => 'arn'], 'CreationTime' => ['shape' => '__timestampIso8601', 'locationName' => 'creationTime'], 'Description' => ['shape' => '__string', 'locationName' => 'description'], 'KafkaVersions' => ['shape' => '__listOf__string', 'locationName' => 'kafkaVersions'], 'LatestRevision' => ['shape' => 'ConfigurationRevision', 'locationName' => 'latestRevision'], 'Name' => ['shape' => '__string', 'locationName' => 'name']], 'required' => ['Description', 'LatestRevision', 'CreationTime', 'KafkaVersions', 'Arn', 'Name']], 'ConfigurationInfo' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'locationName' => 'arn'], 'Revision' => ['shape' => '__long', 'locationName' => 'revision']], 'required' => ['Revision', 'Arn']], 'ConfigurationRevision' => ['type' => 'structure', 'members' => ['CreationTime' => ['shape' => '__timestampIso8601', 'locationName' => 'creationTime'], 'Description' => ['shape' => '__string', 'locationName' => 'description'], 'Revision' => ['shape' => '__long', 'locationName' => 'revision']], 'required' => ['Revision', 'CreationTime']], 'ConflictException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 409]], 'CreateClusterRequest' => ['type' => 'structure', 'members' => ['BrokerNodeGroupInfo' => ['shape' => 'BrokerNodeGroupInfo', 'locationName' => 'brokerNodeGroupInfo'], 'ClientAuthentication' => ['shape' => 'ClientAuthentication', 'locationName' => 'clientAuthentication'], 'ClusterName' => ['shape' => '__stringMin1Max64', 'locationName' => 'clusterName'], 'ConfigurationInfo' => ['shape' => 'ConfigurationInfo', 'locationName' => 'configurationInfo'], 'EncryptionInfo' => ['shape' => 'EncryptionInfo', 'locationName' => 'encryptionInfo'], 'EnhancedMonitoring' => ['shape' => 'EnhancedMonitoring', 'locationName' => 'enhancedMonitoring'], 'KafkaVersion' => ['shape' => '__stringMin1Max128', 'locationName' => 'kafkaVersion'], 'LoggingInfo' => ['shape' => 'LoggingInfo', 'locationName' => 'loggingInfo'], 'NumberOfBrokerNodes' => ['shape' => '__integerMin1Max15', 'locationName' => 'numberOfBrokerNodes'], 'OpenMonitoring' => ['shape' => 'OpenMonitoringInfo', 'locationName' => 'openMonitoring'], 'Tags' => ['shape' => '__mapOf__string', 'locationName' => 'tags']], 'required' => ['BrokerNodeGroupInfo', 'KafkaVersion', 'NumberOfBrokerNodes', 'ClusterName']], 'CreateClusterResponse' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'ClusterName' => ['shape' => '__string', 'locationName' => 'clusterName'], 'State' => ['shape' => 'ClusterState', 'locationName' => 'state']]], 'CreateConfigurationRequest' => ['type' => 'structure', 'members' => ['Description' => ['shape' => '__string', 'locationName' => 'description'], 'KafkaVersions' => ['shape' => '__listOf__string', 'locationName' => 'kafkaVersions'], 'Name' => ['shape' => '__string', 'locationName' => 'name'], 'ServerProperties' => ['shape' => '__blob', 'locationName' => 'serverProperties']], 'required' => ['ServerProperties', 'KafkaVersions', 'Name']], 'CreateConfigurationResponse' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'locationName' => 'arn'], 'CreationTime' => ['shape' => '__timestampIso8601', 'locationName' => 'creationTime'], 'LatestRevision' => ['shape' => 'ConfigurationRevision', 'locationName' => 'latestRevision'], 'Name' => ['shape' => '__string', 'locationName' => 'name']]], 'DeleteClusterRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn'], 'CurrentVersion' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'currentVersion']], 'required' => ['ClusterArn']], 'DeleteClusterResponse' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'State' => ['shape' => 'ClusterState', 'locationName' => 'state']]], 'DescribeClusterOperationRequest' => ['type' => 'structure', 'members' => ['ClusterOperationArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterOperationArn']], 'required' => ['ClusterOperationArn']], 'DescribeClusterOperationResponse' => ['type' => 'structure', 'members' => ['ClusterOperationInfo' => ['shape' => 'ClusterOperationInfo', 'locationName' => 'clusterOperationInfo']]], 'DescribeClusterRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn']], 'required' => ['ClusterArn']], 'DescribeClusterResponse' => ['type' => 'structure', 'members' => ['ClusterInfo' => ['shape' => 'ClusterInfo', 'locationName' => 'clusterInfo']]], 'DescribeConfigurationRequest' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'arn']], 'required' => ['Arn']], 'DescribeConfigurationResponse' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'locationName' => 'arn'], 'CreationTime' => ['shape' => '__timestampIso8601', 'locationName' => 'creationTime'], 'Description' => ['shape' => '__string', 'locationName' => 'description'], 'KafkaVersions' => ['shape' => '__listOf__string', 'locationName' => 'kafkaVersions'], 'LatestRevision' => ['shape' => 'ConfigurationRevision', 'locationName' => 'latestRevision'], 'Name' => ['shape' => '__string', 'locationName' => 'name']]], 'DescribeConfigurationRevisionRequest' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'arn'], 'Revision' => ['shape' => '__long', 'location' => 'uri', 'locationName' => 'revision']], 'required' => ['Revision', 'Arn']], 'DescribeConfigurationRevisionResponse' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'locationName' => 'arn'], 'CreationTime' => ['shape' => '__timestampIso8601', 'locationName' => 'creationTime'], 'Description' => ['shape' => '__string', 'locationName' => 'description'], 'Revision' => ['shape' => '__long', 'locationName' => 'revision'], 'ServerProperties' => ['shape' => '__blob', 'locationName' => 'serverProperties']]], 'EBSStorageInfo' => ['type' => 'structure', 'members' => ['VolumeSize' => ['shape' => '__integerMin1Max16384', 'locationName' => 'volumeSize']]], 'EncryptionAtRest' => ['type' => 'structure', 'members' => ['DataVolumeKMSKeyId' => ['shape' => '__string', 'locationName' => 'dataVolumeKMSKeyId']], 'required' => ['DataVolumeKMSKeyId']], 'EncryptionInTransit' => ['type' => 'structure', 'members' => ['ClientBroker' => ['shape' => 'ClientBroker', 'locationName' => 'clientBroker'], 'InCluster' => ['shape' => '__boolean', 'locationName' => 'inCluster']]], 'EncryptionInfo' => ['type' => 'structure', 'members' => ['EncryptionAtRest' => ['shape' => 'EncryptionAtRest', 'locationName' => 'encryptionAtRest'], 'EncryptionInTransit' => ['shape' => 'EncryptionInTransit', 'locationName' => 'encryptionInTransit']]], 'EnhancedMonitoring' => ['type' => 'string', 'enum' => ['DEFAULT', 'PER_BROKER', 'PER_TOPIC_PER_BROKER']], 'Error' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']]], 'Firehose' => ['type' => 'structure', 'members' => ['DeliveryStream' => ['shape' => '__string', 'locationName' => 'deliveryStream'], 'Enabled' => ['shape' => '__boolean', 'locationName' => 'enabled']], 'required' => ['Enabled']], 'ErrorInfo' => ['type' => 'structure', 'members' => ['ErrorCode' => ['shape' => '__string', 'locationName' => 'errorCode'], 'ErrorString' => ['shape' => '__string', 'locationName' => 'errorString']]], 'ForbiddenException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 403]], 'GetBootstrapBrokersRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn']], 'required' => ['ClusterArn']], 'GetBootstrapBrokersResponse' => ['type' => 'structure', 'members' => ['BootstrapBrokerString' => ['shape' => '__string', 'locationName' => 'bootstrapBrokerString'], 'BootstrapBrokerStringTls' => ['shape' => '__string', 'locationName' => 'bootstrapBrokerStringTls']]], 'InternalServerErrorException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 500]], 'KafkaVersion' => ['type' => 'structure', 'members' => ['Version' => ['shape' => '__string', 'locationName' => 'version'], 'Status' => ['shape' => 'KafkaVersionStatus', 'locationName' => 'status']]], 'KafkaVersionStatus' => ['type' => 'string', 'enum' => ['ACTIVE', 'DEPRECATED']], 'ListClusterOperationsRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn'], 'MaxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'NextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']], 'required' => ['ClusterArn']], 'ListClusterOperationsResponse' => ['type' => 'structure', 'members' => ['ClusterOperationInfoList' => ['shape' => '__listOfClusterOperationInfo', 'locationName' => 'clusterOperationInfoList'], 'NextToken' => ['shape' => '__string', 'locationName' => 'nextToken']]], 'ListClustersRequest' => ['type' => 'structure', 'members' => ['ClusterNameFilter' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'clusterNameFilter'], 'MaxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'NextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListClustersResponse' => ['type' => 'structure', 'members' => ['ClusterInfoList' => ['shape' => '__listOfClusterInfo', 'locationName' => 'clusterInfoList'], 'NextToken' => ['shape' => '__string', 'locationName' => 'nextToken']]], 'ListConfigurationRevisionsRequest' => ['type' => 'structure', 'members' => ['Arn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'arn'], 'MaxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'NextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']], 'required' => ['Arn']], 'ListConfigurationRevisionsResponse' => ['type' => 'structure', 'members' => ['NextToken' => ['shape' => '__string', 'locationName' => 'nextToken'], 'Revisions' => ['shape' => '__listOfConfigurationRevision', 'locationName' => 'revisions']]], 'ListConfigurationsRequest' => ['type' => 'structure', 'members' => ['MaxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'NextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListConfigurationsResponse' => ['type' => 'structure', 'members' => ['Configurations' => ['shape' => '__listOfConfiguration', 'locationName' => 'configurations'], 'NextToken' => ['shape' => '__string', 'locationName' => 'nextToken']]], 'ListKafkaVersionsRequest' => ['type' => 'structure', 'members' => ['MaxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'NextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListKafkaVersionsResponse' => ['type' => 'structure', 'members' => ['KafkaVersions' => ['shape' => '__listOfKafkaVersion', 'locationName' => 'kafkaVersions'], 'NextToken' => ['shape' => '__string', 'locationName' => 'nextToken']]], 'ListNodesRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn'], 'MaxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'NextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']], 'required' => ['ClusterArn']], 'ListNodesResponse' => ['type' => 'structure', 'members' => ['NextToken' => ['shape' => '__string', 'locationName' => 'nextToken'], 'NodeInfoList' => ['shape' => '__listOfNodeInfo', 'locationName' => 'nodeInfoList']]], 'ListTagsForResourceRequest' => ['type' => 'structure', 'members' => ['ResourceArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'resourceArn']], 'required' => ['ResourceArn']], 'ListTagsForResourceResponse' => ['type' => 'structure', 'members' => ['Tags' => ['shape' => '__mapOf__string', 'locationName' => 'tags']]], 'MaxResults' => ['type' => 'integer', 'min' => 1, 'max' => 100], 'LoggingInfo' => ['type' => 'structure', 'members' => ['BrokerLogs' => ['shape' => 'BrokerLogs', 'locationName' => 'brokerLogs']], 'required' => ['BrokerLogs']], 'MutableClusterInfo' => ['type' => 'structure', 'members' => ['BrokerEBSVolumeInfo' => ['shape' => '__listOfBrokerEBSVolumeInfo', 'locationName' => 'brokerEBSVolumeInfo'], 'ConfigurationInfo' => ['shape' => 'ConfigurationInfo', 'locationName' => 'configurationInfo'], 'NumberOfBrokerNodes' => ['shape' => '__integer', 'locationName' => 'numberOfBrokerNodes'], 'OpenMonitoring' => ['shape' => 'OpenMonitoring', 'locationName' => 'openMonitoring'], 'EnhancedMonitoring' => ['shape' => 'EnhancedMonitoring', 'locationName' => 'enhancedMonitoring'], 'LoggingInfo' => ['shape' => 'LoggingInfo', 'locationName' => 'loggingInfo']]], 'NodeInfo' => ['type' => 'structure', 'members' => ['AddedToClusterTime' => ['shape' => '__string', 'locationName' => 'addedToClusterTime'], 'BrokerNodeInfo' => ['shape' => 'BrokerNodeInfo', 'locationName' => 'brokerNodeInfo'], 'InstanceType' => ['shape' => '__string', 'locationName' => 'instanceType'], 'NodeARN' => ['shape' => '__string', 'locationName' => 'nodeARN'], 'NodeType' => ['shape' => 'NodeType', 'locationName' => 'nodeType'], 'ZookeeperNodeInfo' => ['shape' => 'ZookeeperNodeInfo', 'locationName' => 'zookeeperNodeInfo']]], 'NodeType' => ['type' => 'string', 'enum' => ['BROKER']], 'NotFoundException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 404]], 'ServiceUnavailableException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 503]], 'StateInfo' => ['type' => 'structure', 'members' => ['Code' => ['shape' => '__string', 'locationName' => 'code'], 'Message' => ['shape' => '__string', 'locationName' => 'message']]], 'StorageInfo' => ['type' => 'structure', 'members' => ['EbsStorageInfo' => ['shape' => 'EBSStorageInfo', 'locationName' => 'ebsStorageInfo']]], 'TagResourceRequest' => ['type' => 'structure', 'members' => ['ResourceArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'resourceArn'], 'Tags' => ['shape' => '__mapOf__string', 'locationName' => 'tags']], 'required' => ['ResourceArn', 'Tags']], 'Tls' => ['type' => 'structure', 'members' => ['CertificateAuthorityArnList' => ['shape' => '__listOf__string', 'locationName' => 'certificateAuthorityArnList']]], 'TooManyRequestsException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 429]], 'UnauthorizedException' => ['type' => 'structure', 'members' => ['InvalidParameter' => ['shape' => '__string', 'locationName' => 'invalidParameter'], 'Message' => ['shape' => '__string', 'locationName' => 'message']], 'exception' => \true, 'error' => ['httpStatusCode' => 401]], 'UntagResourceRequest' => ['type' => 'structure', 'members' => ['ResourceArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'resourceArn'], 'TagKeys' => ['shape' => '__listOf__string', 'location' => 'querystring', 'locationName' => 'tagKeys']], 'required' => ['TagKeys', 'ResourceArn']], 'UpdateBrokerCountRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn'], 'CurrentVersion' => ['shape' => '__string', 'locationName' => 'currentVersion'], 'TargetNumberOfBrokerNodes' => ['shape' => '__integerMin1Max15', 'locationName' => 'targetNumberOfBrokerNodes']], 'required' => ['ClusterArn', 'CurrentVersion', 'TargetNumberOfBrokerNodes']], 'UpdateBrokerCountResponse' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'ClusterOperationArn' => ['shape' => '__string', 'locationName' => 'clusterOperationArn']]], 'UpdateBrokerStorageRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn'], 'CurrentVersion' => ['shape' => '__string', 'locationName' => 'currentVersion'], 'TargetBrokerEBSVolumeInfo' => ['shape' => '__listOfBrokerEBSVolumeInfo', 'locationName' => 'targetBrokerEBSVolumeInfo']], 'required' => ['ClusterArn', 'TargetBrokerEBSVolumeInfo', 'CurrentVersion']], 'UpdateBrokerStorageResponse' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'ClusterOperationArn' => ['shape' => '__string', 'locationName' => 'clusterOperationArn']]], 'UpdateClusterConfigurationRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn'], 'ConfigurationInfo' => ['shape' => 'ConfigurationInfo', 'locationName' => 'configurationInfo'], 'CurrentVersion' => ['shape' => '__string', 'locationName' => 'currentVersion']], 'required' => ['ClusterArn', 'CurrentVersion', 'ConfigurationInfo']], 'UpdateClusterConfigurationResponse' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'ClusterOperationArn' => ['shape' => '__string', 'locationName' => 'clusterOperationArn']]], 'UpdateMonitoringRequest' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'clusterArn'], 'CurrentVersion' => ['shape' => '__string', 'locationName' => 'currentVersion'], 'EnhancedMonitoring' => ['shape' => 'EnhancedMonitoring', 'locationName' => 'enhancedMonitoring'], 'OpenMonitoring' => ['shape' => 'OpenMonitoringInfo', 'locationName' => 'openMonitoring'], 'LoggingInfo' => ['shape' => 'LoggingInfo', 'locationName' => 'loggingInfo']], 'required' => ['ClusterArn', 'CurrentVersion']], 'UpdateMonitoringResponse' => ['type' => 'structure', 'members' => ['ClusterArn' => ['shape' => '__string', 'locationName' => 'clusterArn'], 'ClusterOperationArn' => ['shape' => '__string', 'locationName' => 'clusterOperationArn']]], 'ZookeeperNodeInfo' => ['type' => 'structure', 'members' => ['AttachedENIId' => ['shape' => '__string', 'locationName' => 'attachedENIId'], 'ClientVpcIpAddress' => ['shape' => '__string', 'locationName' => 'clientVpcIpAddress'], 'Endpoints' => ['shape' => '__listOf__string', 'locationName' => 'endpoints'], 'ZookeeperId' => ['shape' => '__double', 'locationName' => 'zookeeperId'], 'ZookeeperVersion' => ['shape' => '__string', 'locationName' => 'zookeeperVersion']]], 'OpenMonitoring' => ['type' => 'structure', 'members' => ['Prometheus' => ['shape' => 'Prometheus', 'locationName' => 'prometheus']], 'required' => ['Prometheus']], 'OpenMonitoringInfo' => ['type' => 'structure', 'members' => ['Prometheus' => ['shape' => 'PrometheusInfo', 'locationName' => 'prometheus']], 'required' => ['Prometheus']], 'Prometheus' => ['type' => 'structure', 'members' => ['JmxExporter' => ['shape' => 'JmxExporter', 'locationName' => 'jmxExporter'], 'NodeExporter' => ['shape' => 'NodeExporter', 'locationName' => 'nodeExporter']]], 'PrometheusInfo' => ['type' => 'structure', 'members' => ['JmxExporter' => ['shape' => 'JmxExporterInfo', 'locationName' => 'jmxExporter'], 'NodeExporter' => ['shape' => 'NodeExporterInfo', 'locationName' => 'nodeExporter']]], 'S3' => ['type' => 'structure', 'members' => ['Bucket' => ['shape' => '__string', 'locationName' => 'bucket'], 'Enabled' => ['shape' => '__boolean', 'locationName' => 'enabled'], 'Prefix' => ['shape' => '__string', 'locationName' => 'prefix']], 'required' => ['Enabled']], 'JmxExporter' => ['type' => 'structure', 'members' => ['EnabledInBroker' => ['shape' => '__boolean', 'locationName' => 'enabledInBroker']], 'required' => ['EnabledInBroker']], 'JmxExporterInfo' => ['type' => 'structure', 'members' => ['EnabledInBroker' => ['shape' => '__boolean', 'locationName' => 'enabledInBroker']], 'required' => ['EnabledInBroker']], 'NodeExporter' => ['type' => 'structure', 'members' => ['EnabledInBroker' => ['shape' => '__boolean', 'locationName' => 'enabledInBroker']], 'required' => ['EnabledInBroker']], 'NodeExporterInfo' => ['type' => 'structure', 'members' => ['EnabledInBroker' => ['shape' => '__boolean', 'locationName' => 'enabledInBroker']], 'required' => ['EnabledInBroker']], '__boolean' => ['type' => 'boolean'], '__blob' => ['type' => 'blob'], '__double' => ['type' => 'double'], '__integer' => ['type' => 'integer'], '__integerMin1Max15' => ['type' => 'integer', 'min' => 1, 'max' => 15], '__integerMin1Max16384' => ['type' => 'integer', 'min' => 1, 'max' => 16384], '__listOfBrokerEBSVolumeInfo' => ['type' => 'list', 'member' => ['shape' => 'BrokerEBSVolumeInfo']], '__listOfClusterInfo' => ['type' => 'list', 'member' => ['shape' => 'ClusterInfo']], '__listOfClusterOperationInfo' => ['type' => 'list', 'member' => ['shape' => 'ClusterOperationInfo']], '__listOfConfiguration' => ['type' => 'list', 'member' => ['shape' => 'Configuration']], '__listOfConfigurationRevision' => ['type' => 'list', 'member' => ['shape' => 'ConfigurationRevision']], '__listOfKafkaVersion' => ['type' => 'list', 'member' => ['shape' => 'KafkaVersion']], '__listOfNodeInfo' => ['type' => 'list', 'member' => ['shape' => 'NodeInfo']], '__listOf__string' => ['type' => 'list', 'member' => ['shape' => '__string']], '__long' => ['type' => 'long'], '__mapOf__string' => ['type' => 'map', 'key' => ['shape' => '__string'], 'value' => ['shape' => '__string']], '__string' => ['type' => 'string'], '__stringMin1Max128' => ['type' => 'string', 'min' => 1, 'max' => 128], '__stringMin1Max64' => ['type' => 'string', 'min' => 1, 'max' => 64], '__stringMin5Max32' => ['type' => 'string', 'min' => 5, 'max' => 32], '__timestampIso8601' => ['type' => 'timestamp', 'timestampFormat' => 'iso8601']]];
