#!/usr/bin/Rscript --vanilla
#R --vanilla fichier.csv fichier_de_sortie.csv < make_duplica.R

argv<- commandArgs()
file <- argv[3]
output <- argv[4]


Table=read.csv(file, sep=";", header=TRUE, dec=".")
Matrice <- as.matrix(Table)

col <- ncol(Matrice)
row <- nrow(Matrice)

minimums <- Matrice[1,-1]; minimums
maximums <- Matrice[nrow(Matrice),-1]

Matrice <- Matrice[-row,]; Matrice
Matrice <- Matrice[-1,]; Matrice

concentrations <- Matrice[,1]
concentrations <- rep(concentrations, 2)

Matrice2 <- as.matrix(concentrations)

columns_names <- c(colnames(Matrice)[1])
Matrice <- Matrice[,-1]          

min_list <- c(NA)
max_list <- c(NA)


for (i in 1:20){
  if (i%%2==1){
    columns_names <- c(columns_names, colnames(Matrice)[i])
    col1 <- Matrice[,i]
    min1 <- minimums[i]
    max1 <- maximums[i]
  }else{
    col2 <- Matrice[,i];col2
    min2 <- minimums[i]
    max2 <- maximums[i]
    MIN <- (min1+min2)/2
    MAX <- (max1+max2)/2
    min_list <- c(min_list,MIN)
    max_list <- c(max_list,MAX)
    result <- c(col1,col2)
    Matrice2 <- cbind (Matrice2,result)
  }
}


Matrice2 <- rbind(min_list,Matrice2,max_list)
colnames(Matrice2) <- columns_names
rownames(Matrice2) <- NULL


datas <- data.frame (Matrice2)


write.table(datas, output, quote=FALSE, sep=";", dec=".", row.names = FALSE)



