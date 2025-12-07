describe("TC-FS-07", () => {
    it("TC-FS-07", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");

        cy.contains(".files-fileList tr", "Documents")
            .find(".action-share")
            .click();

        cy.contains("button", "Share with user").click();
        cy.get('input[placeholder*="Name or email"]').type("abdulhaseeb.39393@gmail.com");
        cy.contains(".option__lineone", "abdulhaseeb.39393@gmail.com").click();

        cy.get('input[type="checkbox"]').each(($checkbox) => {
            cy.wrap($checkbox).check();
        });

        cy.contains("button", "Share").click();

        cy.get(".share-item").should("be.visible");
        cy.get(".share-permissions").should("contain", "all");
    });
});