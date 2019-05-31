#!/usr/bin/Rscript --vanilla
#R --vanilla fichier.csv MIN MAX destination < multi_llogistic.R

library(drc)
library(ggplot2)
library(magrittr)


argv<- commandArgs()
file <- argv[3]
p_min <- argv[4]
p_max <- arv[5]
destination <- arg[6]


if (p_min=="NA" & p_max=="NA"){
  test="LL4"
  c <- NA
  d <- NA
}else if (p_min=="NA" & p_max!="NA"){
  test="LL3u"
  c <- NA
  d <- as.numeric(p_max)
}else if (p_min!="NA" & p_max=="NA"){
  test="LL3"
  c <- as.numeric(p_min)
  d <- NA
}else{
  test="LL2"
  c <- as.numeric(p_min)
  d <- as.numeric(p_max)  
}

name_files <- paste(destination,test, sep="_")

T=read.table(file.choose(), sep=";", header=TRUE, dec=".")

M <- as.matrix(T)


columns_names <- c("none")
minimums <- M[1,]
maximums <- M[nrow(M),]

M <- M[-1,]
M <- M[-nrow(M),]


abscisse <- colnames(M)[1]
vector_c <- M[,1]
matrice <- matrix(c(0,0,0,0))

CMAX <- as.numeric(vector_c[1])

for (i in 2:ncol(M)) {
  vector_s <- M[,i]
  datas <- data.frame (concentration = vector_c, signal = vector_s)
  columns_names[i] <- colnames(M)[i]
  
  
  expr <- try(
    curved_fit <- drm(
      formula = signal ~ concentration,
      data = datas,
      fct = LL.4(fixed = c(NA, c, d, NA), names = c("hill", "min", "max",  "ec50"))
    )    
  ) 
  
  
  graphic_name = paste(name_files,columns_names[i],".png", sep="_")
  
  if (class(expr)!="try-error") {
    
    newdata <- expand.grid(concentration=(seq(0.39,200,length=10000)))
    pm <- predict (curved_fit, newdata=newdata, interval="confidence")
    
    newdata$p <- pm[,1]
    newdata$pmin <- pm[,2]
    newdata$pmax <- pm[,3]
    
    
    results <- curved_fit$fit$par
    additional_data <- data.frame(concentration = seq(0.39, CMAX, length.out = 10000))
    curved_counts <- predict(curved_fit, newdata = additional_data)
    curved_fitted_data <- data.frame(concentration = additional_data, signal = curved_counts)
    
    
    ggplot(datas, aes(x = concentration, y = signal)) +
      ggtitle(columns_names[i])+
      xlab (colnames(M)[1]) +
      geom_point() +
      geom_line(data = curved_fitted_data) +
      geom_ribbon(data=newdata, aes(x=concentration, y=p, ymin=pmin, ymax=pmax), alpha=0.2) +
      ylim(-5, NA) +
      scale_x_continuous(trans = "log10") 
      ggsave(graphic_name, width = 11, height = 8)
    
    
  }else{
    vector_r <- c("NA","NA","NA","NA")
    ggplot(datas, aes(x = concentration, y = signal)) +
      ggtitle(columns_names[i])+
      xlab (colnames(M)[1]) +
      geom_point() +
      ylim(-5, NA) +
      scale_x_continuous(trans = "log10") +
      ggsave(graphic_name, width = 11, height = 8)
  }
  
  if (test=="LL4"){
    vector_r <- c(results[2],results[3],results[1],results[4])
  }else if (test=="LL3u"){
    vector_r <- c(results[2], p_max, results[1],results[3])
  }else if (test=="LL3"){
    vector_r <- c(p_min, results[2],results[1],results[3])  
  }else{
    vector_r <- c(p_min, p_max, results[1],results[2])
  }
  
  matrice <- cbind(matrice,vector_r)
}  

rownames(matrice)<-c("min","max","hill","ec50")
colnames(matrice)<-columns_names
matrice <- matrice[,-1]

final_results <- data.frame (matrice)


file_output <- paste(name_files,".csv", sep = "")

write.table(final_results,file_output, quote=FALSE, sep=";", dec=",")







