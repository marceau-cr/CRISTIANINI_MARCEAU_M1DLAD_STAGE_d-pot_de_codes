#!/usr/bin/Rscript --vanilla
#R --vanilla fichier.csv fichier_de_sortie.csv < normalization.R

argv<- commandArgs()
file <- argv[3]
output <- argv[4]


Table=read.csv(file, sep=";", header=TRUE, dec=".")
Matrice <- as.matrix(Table)
col <- ncol(Matrice)
row <- nrow(Matrice)

for (j in 2:col){
  mini <- Matrice[1,j]
  maxi <- Matrice[row,j]
  Matrice [1,j] <- 0
  Matrice [row,j] <- 100
  print (Matrice)
  for (i in 2:(row-1)){
    x <- Matrice[i,j]
    Matrice[i,j] <- (((x-mini)*100)/(maxi-mini))
  }
}

datas <- data.frame (Matrice)

write.table(datas, output, quote=FALSE, sep=";", dec=".", row.names = FALSE)
