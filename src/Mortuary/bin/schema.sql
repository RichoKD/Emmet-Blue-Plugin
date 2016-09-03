CREATE SCHEMA Mortuary;
GO

CREATE TABLE Mortuary.Body (
	BodyID INTEGER PRIMARY KEY IDENTITY NOT NULL,
	DeathPhysicianID INTEGER,
	BodyTag VARCHAR(50) NOT NULL,
	DateOfDeath DATE NOT NULL,
	PlaceOfDeath VARCHAR(100) NOT NULL
)
GO

CREATE TABLE Mortuary.BodyInformation (
	BodyInformationID INTEGER PRIMARY KEY IDENTITY NOT NULL,
	BodyID INTEGER,
	BodyFullName VARCHAR(20) NOT NULL,
	BodyDateOfBirth DATE,
	BodyGender VARCHAR(10) NOT NULL,
	BodyStatus BIT,
	BodyNextOfKinFullName VARCHAR(50),
	BodyNextOfKinAddress VARCHAR(100),
	BodyNextOfKinPhoneNumber VARCHAR(15)
	FOREIGN KEY (BodyID) REFERENCES Mortuary.Body(BodyID) ON UPDATE CASCADE ON DELETE CASCADE
)
GO
CREATE TABLE Mortuary.DepositorDetails(
	DepositorDetailsID INTEGER PRIMARY KEY IDENTITY NOT NULL,
	BodyID INTEGER,
	DepositorFullName VARCHAR(20),
	DepositorAddress VARCHAR (max),
	DepositorRelationshipType VARCHAR(20),
	DepositorPhoneNumber VARCHAR(20),
	FOREIGN KEY (BodyID) REFERENCES Mortuary.Body(BodyID) ON UPDATE CASCADE ON DELETE CASCADE
)
GO